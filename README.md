1 ==> utiliser la commande Make pour exécuter le programme
{
    * make all : pour exécuter le programme
    {
        make clean : pour supprimer les fichiers générés par le programme et down all containers
        make check pour verifier si docker est installé ou non
        make docker-setup  : pour nettyer les images et les containers precedents pour evite les conflits
        make wait for wait the containers to be ready
        make setup_worpdress pour installer wordpress et mysql ajoter pluguin 
        make verify pour verifier si le programme est bien installé
    }

}
2 ==> le programme est base sur une image docker qui contient wordpress et mysql (hint manqderch nkhdem b wordpress ou mysql directement aykhssni cloud)
2 * Cette fonction répond aux messages en utilisant les mots-clés suivants :
    {
        "bonjour" : le bot répond "cava ?"
        "hamd et toi ?" : le bot répond "cava hamd fin chada ?"
        "thala frassek !" : le bot répond "Au revoir ! Passez une bonne journée !"
        Autres messages : si aucun mot-clé n’est trouvé, le bot répond "Je suis désolé, je n'ai pas compris votre demande."
    }
    