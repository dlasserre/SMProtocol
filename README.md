# SMProtocol
## download manager
-  1 - What is SMProtocol (download manager) ?
-  2 - Requirements 
-  3 - How to install SMProtocol
-  4 - System operation
-  5 - UML model, save in noSQL database (MongoDB)
-  6 - Understand SMProtocol log output
-  7 - Reference

### 1 - What is SMProtocol (download manager) ?
SMProtocol is a download manager and stat tracker for your download, if you want to know how many download was completed, how many time to download file, geo-location download, get ip information, the reason of end download after terminated, and more... SMProtocol is for you !

By default apache do not has interpret the partial download (HTTP 206 code), SM Protocol accept this feature by default  ! if your client want to put in pause the download and restart later it's possible ! nice functionality you don't think no ?

*SMB Protocol is a daemon is independent of other programs*

### 2 - Requirements:

SMProtocol written in **PHP source code**, the PHP version is **5.3.X**, if you want to use the stats tracking SMProtocol (*download manager*) use an Mysql server and PDO connection between PHP en Mysql.

Mysql server version is 5.X.

For data caching SMProtocol use Memcache server, the version of memcache is  actually 1.4.13

SMProtocol is a PHP deamon, please check if this extension was loaded: 
 - 1 PCNTL extension for fork
 - 2 Memcache extension for caching data
 - 3 Socket extension (by default normaly)

### 3 - How to install SMProtocol ?

For installing SMProtocol is very faster and simply, please clone git via [https://github.com/dlasserre/SMProtocol.git](https://github.com/dlasserre/SMProtocol.git/ "Git repository") and open this file SMProtocol/protocol/tcp/definition.php and read the comments :
```
<?php
/** Namespace protocol\tcp */
namespace protocol\tcp;

/**
 * Class interpret
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package protocol\tcp
 */
class definition extends \library\SMProtocol\abstracts\definition
{
    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     */
    public function __construct()
    {
        /** the ip, domain when server will be listened **/
        $this->host = '127.0.0.1';
        /** the port where server will binded **/
        $this->port = 8081;
    }

    public function exception(\Exception $exception)
    {
        /** Log exception here ... */
    }
} 

```
#### More configuration:

*(I advise you not to change)*
>* max_connection: Max client connection by fork (default 2)
>* block_size: Black size for response (default 512 bit)

** If you want to forward to other service **
>* forward_host: forwarded host
>* forward_port: forwarded port

** socket configuration **
*only for expert user more informations follow this link [http://php.net/manual/fr/book.sockets.php](PHP socket "Socket")*
>* socket_domain: type of socket, default AF_INET
>* socket_type: default SOCK_STREAM
>* socket_protocol: SOL_TCP

To run deamon enter this command in your terminal: `` /usr/bin/php SMProtocol.php `` or `` ./SMProtocol ``
Check your proccess list: 
```
891 pts/2    S+     0:00 /usr/bin/php ./SMProtocol.php
893 pts/2    S+     0:00 /usr/bin/php ./SMProtocol.php

```
*I explain in the third party why you see two process in list.*

### 4 - System operation

SMProtocol is based on php socket and **"fork concept"**, but the very important part is the process manager and socket, SMProtocol use the system call 'socket_select' RTFM :) if you want more information.

Each new connection SMProtocol count the number simultanemous connection in process, if count is superior at max_connection parameter, SMP fork new process with the current connection, clean connection in parent and after accept the new connection.

Each childrens or parent if connection not exceeds 2, used socket select to manage multiple socket by process, the select function is more better than multi-threading (is my personaly thinking).

####Please see the workflow bellow:

![Alt text](http://img11.hostingpics.net/pics/305492UntitledDiagram.jpg "Workflow")

After children wipe, no defunct process, the process manage is really optimized :) normaly...

### 5 - Database model

Please see this UML bellow:
![Alt text](http://img11.hostingpics.net/pics/187023diagram.png "MCD")

### 6 - Understand SMProtocol log output
SMProtocol return more information in standard output, if you want to trace a problème or bug please check the log and create a ticket in gitticket manager.

**exemple of output**: 
```
    _____ __  _______             __                   __
   / ___//  |/  / __ \_________  / /_____  _________  / /
   \__ \/ /|_/ / /_/ / ___/ __ \/ __/ __ \/ ___/ __ \/ /
  ___/ / /  / / ____/ /  / /_/ / /_/ /_/ / /__/ /_/ / /
 /____/_/  /_/_/   /_/   \____/\__/\____/\___/\____/_/

------------ tcp ------------
[tcp] Starting...
[tcp] Class Hook loaded
[tcp] Garbage Collector...OK
[tcp] Success: detached with pid <893>, parent pid <891>

[tcp] Binding on 127.0.0.1:8081.............
[tcp] Running success
Connection received from 127.0.0.1 on port 45649
[tcp] <<< 338 bytes from <127.0.0.1:45649>
[tcp] File downloaded "Capture.png" 
[tcp] OK 200 Response send
in memcache
[tcp] >>> 159679 bytes to <127.0.0.1:45649@pid:893>: 
[tcp] Connected closed with message "Success"
[tcp]Garbage Collector: Number of cycle collected < 0 >
[tcp] Save download(s) tracker in database.
[tcp] Connection with all clients was terminated and closed, now save stats in database.
[survey:893] ip <80.250.29.169> already exist with id <7>
[survey:893] file <Capture.png> exist with id <2>
[survey:893] download was saved with id <173>
[survey:893] location was saved
[survey:893] headers was saved
[survey:893] pid was saved
[survey:893] debug was saved
[tcp] Save download  completed.
[unset] Unset _instance
[unset] Unset _stores
[unset] Unset http_response
[unset] Unset start_at
[unset] Unset end_at
[unset] Unset completed
[unset] Unset percent
[unset] Unset bytes_send
[unset] Unset _file
[unset] Unset _headers
[unset] Unset _http_requests
```
Is very important to trace log for any problems.