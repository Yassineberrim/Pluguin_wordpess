#include <errno.h>
#include <string.h>
#include <unistd.h>
#include <netdb.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <stdlib.h>
#include <stdio.h>

int sockfd, connfd, max_fd;
struct sockaddr_in servaddr, cli;
socklen_t len;

int id[100000];
char *msgs[100000], read_buf[1024], write_buf[50];
fd_set fd_master, fd_read, fd_write;

int git = 0;



int extract_message(char **buf, char **msg)
{
    char *newbuf;
    int i;

    *msg = 0;
    if (*buf == 0)
        return (0);
    i = 0;
    while ((*buf)[i])
    {
        if ((*buf)[i] == '\n')
        {
            newbuf = calloc(1, sizeof(*newbuf) * (strlen(*buf + i + 1) + 1));
            if (newbuf == 0)
                return (-1);
            strcpy(newbuf, *buf + i + 1);
            *msg = *buf;
            (*msg)[i + 1] = 0;
            *buf = newbuf;
            return (1);
        }
        i++;
    }
    return (0);
}

char *str_join(char *buf, char *add)
{
    char *newbuf;
    int len;

    if (buf == 0)
        len = 0;
    else
        len = strlen(buf);
    newbuf = malloc(sizeof(*newbuf) * (len + strlen(add) + 1));
    if (newbuf == 0)
        return (0);
    newbuf[0] = 0;
    if (buf != 0)
        strcat(newbuf, buf);
    free(buf);
    strcat(newbuf, add);
    return (newbuf);
}

void fatal()
{
    write(2, "Fatal error\n", strlen("Fatal error\n"));
    exit(1);
}

void broadcast(int sender_fd, char *msg)
{
    for (int fd = 0; fd <= max_fd; fd++)
    {
        if (fd != sender_fd && FD_ISSET(fd, &fd_write))
            send(fd, msg, strlen(msg), 0);
    }
}

void connect_client(int fd)
{
    if (fd > max_fd)
        max_fd = fd;
    id[fd] = git++;
    msgs[fd] = NULL;
    sprintf(write_buf, "server: client %d just arrived\n", id[fd]);
    broadcast(fd, write_buf);
    FD_SET(fd, &fd_master);
}

void disconnect_client(int fd)
{
    sprintf(write_buf, "server: client %d just left\n", id[fd]);
    broadcast(fd, write_buf);
    FD_CLR(fd, &fd_master);
    close(fd);
}

void send_msg(int fd)
{
    char *msg = 0;
    while (extract_message(&msgs[fd], &msg))
    {
        sprintf(write_buf, "client %d: ", id[fd]);
        broadcast(fd, write_buf);
        broadcast(fd, msg);
        free(msg);
    }
}

void run_server()
{
    FD_ZERO(&fd_master);
    FD_SET(sockfd, &fd_master);
    max_fd = sockfd;

    while (1)
    {
        fd_read = fd_write = fd_master;
        if (select(max_fd + 1, &fd_read, &fd_write, NULL, NULL) < 0)
            fatal();
        for (int fd = 0; fd <= max_fd; fd++)
        {
            if (!FD_ISSET(fd, &fd_read))
                continue;
            if (fd == sockfd)
            {
                len = sizeof(cli);
                connfd = accept(sockfd, (struct sockaddr *)&cli, &len);
                if (connfd >= 0)
                {
                    connect_client(connfd);
                    break;
                }
            }
            else
            {
                int size = recv(fd, read_buf, 1023, 0);
                if (size <= 0)
                {
                    disconnect_client(fd);
                    break;
                }
                read_buf[size] = 0;
                msgs[fd] = str_join(msgs[fd], read_buf);
                send_msg(fd);
            }
        }
    }
}

int main(int ac, char **av)
{
    if (ac != 2)
    {
        write(2, "Wrong number of arguments\n", strlen("Wrong number of arguments\n"));
        return 1;
    }
    sockfd = socket(AF_INET, SOCK_STREAM, 0);
    if (sockfd == -1)
        fatal();
    bzero(&servaddr, sizeof(servaddr));

    servaddr.sin_family = AF_INET;
    servaddr.sin_addr.s_addr = htonl(2130706433);
    servaddr.sin_port = htons(atoi(av[1]));

    if ((bind(sockfd, (const struct sockaddr *)&servaddr, sizeof(servaddr))) != 0)
        fatal();
    if (listen(sockfd, 10) != 0)
        fatal();
    run_server();
    return 0;
}