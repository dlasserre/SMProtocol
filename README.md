SMProtocol
=========

SMProtocol est un Framework de protocols, il permet de recycler les protocols ou même de créer vos propre protocol (TEXT).
Exemple : SMTP, HTTP, SIP, FTP, etc...

Vous pouvez binder des hooks sur les commandes d'un protocol ou même sur une suite commande, SMProtocol est capable d'être transparent et être utilisé comme proxy, faire des stats, déclancher des action, interagire avec d'autre applicatif en temps réel.

SMProtocol est multi-forké, chaque protocol est détaché d'un fork qui sont tous ralié au parent SMP, cela implique que les protocols sont tous indépendant les uns des autres, chaqu'un de ces processus fork une nouvelle fois a chaque connection entrantes, bien evidament la gestions des fork a été très refléchit (aucun risque de fork bombe ou de processus zombie...).

La gestion des signaux est compléte par exemple envoyer un signal de type SIGHUP au processus SMP va faire un redémarage de tous les server (protocols) alors qu'un signal SIGHUP sur un des processus fils de SMP donc un protocol va faire un simple redémarage de celui si, cela permet en faite que vous pouvez modifier le code d'un protocol sans tous redémarer et rendre les autre server indisponnible.


![alt tag](https://imagizer.imageshack.us/v2/899x424q90/819/ip2s.png)

Version
----

1.0

Tech
-----------

Prérequis:

* [PHP5.X] - Une version 5.X requise car utilisation des namespace, ainsi que POO
* [PHP-SOCKET] - Le support socket doit être activé.
* [PNCLT FUNCTION] - Les méthodes PCNTL pour la gestion des process et des signaux ainsi que les methodes POSIX.

Installation
--------------

```sh
git clone [git-repo-url] SMProtocol
cd SMProtocol/protocol
mkdir [protocol-name]
cd [protocol-name]
touch interpret.php
emacs interpret.php
```

```php
/**
* @author Damien Lasserre <damien.lasserre@gmail.com>
* Votre class doit OBLIGATOIREMENT implementer l'interface et etendre definition !
*/
class interpret extends definition implements \protocol\interfaces\interpret
{
    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @description parametrage du server.
     * Référez vous au fichier définition pour connaitre les différents parametres.
     */
    public function __construct()
    {
        $this->host = '127.0.0.1';
        $this->port = 4243;
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param socket $socket
     *
     * @description C'est ici que vous allez pouvoir jouer la transmission.
     */
    public function transmission(socket $socket)
    {
        /** La methode ping envoi des données mais n'attend pas de retour. **/
        $socket->ping('HELO'.PHP_EOL);
        /** La methode pingPong envoi des données mais attend une réponse.**/
        $_response =$socket->pingPong('SAVA'.PHP_EOL);
        echo $_response.PHP_EOL;
        /** La méthode pong recoit des données **/
        while($data = $socket->pong()) {
            $_buffer .= $data;
        }
    }

    /**
    * @description cette methode vous permet de gerer vos exception levé par
    * soit par 'SMProtocol', le server, la socket ou le client.
    */
    public function exception(\Exception $exception)
    {

    }
}
```

```html
Votre class doit OBLIGATOIREMENT implementer l'interface et etendre 'definition' !
```


License
----

GNU


**Free Software, Hell Yeah!**
