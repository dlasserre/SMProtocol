SMProtocol
=========

SMProtocol est un Framework de protocols, il permet de recycler les protocols ou même de créer vos propre protocol (TEXT).
Exemple : SMTP, HTTP, SIP, FTP, etc...

Vous pouvez binder des hooks sur les commandes d'un protocol ou même sur une suite commande, SMProtocol est capable d'être transparent et être utilisé comme proxy, faire des stats, déclancher des action, interagire avec d'autre applicatif en temps réel.

SMProtocol est multi-forké, chaque protocol est détaché d'un fork qui sont tous ralié au parent SMP, cela implique que les protocols sont tous indépendant les uns des autres, chaqu'un de ces processus utilise un select pour la gestion des multi-socket, bien evidament la gestions des fork a été très refléchit ainsi que celle des socket (aucun risque de fork bombe ou de processus zombie... ni socket pas close).

La gestion des signaux est compléte par exemple envoyer un signal de type SIGHUP au processus SMP va faire un redémarage de tous les server (protocols) alors qu'un signal SIGHUP sur un des processus fils de SMP donc un protocol va faire un simple redémarage de celui si, cela permet en faite que vous pouvez modifier le code d'un protocol sans tous redémarer et rendre les autre services indisponnible.


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
/** Namespace */
namespace smtp;

/**
 * Class smtp
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package hook
 */
class hook extends \hook
{
    /** @var  string $_from */
    protected $_from;
    /** @var  string $_to */
    protected $_to;
    /** ... **/

    public function preDispatch($address, $port)
    {
        echo 'Connection received from '.$address.' on port '.$port.PHP_EOL;
    }

    public function dispatch($input)
    {
        echo $input.PHP_EOL;
        $this->send('OK 250'.PHP_EOL);
    }

    public function postDispatch()
    {
        /** forwarding, or other action... **/
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
