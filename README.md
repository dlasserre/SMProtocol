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

SMProtocol written in **PHP source code**, the PHP version is **5.3.X**, if you want to use the stats tracking SMProtocol (*download manager*) use an MongoDB server and mongo.so extension for connection between PHP en MongoServer.

MongoServer server version is 2.6.4

For data caching SMProtocol use Memcache server, the version of memcache is  actually 1.4.13

SMProtocol is a PHP daemon, please check if this extension was loaded: 
 - 1 PCNTL extension for fork
 - 2 Memcache extension for caching data
 - 3 Socket extension (by default normally)
 - 4 Mongo extension for PHP.
 - 5 Optionally: Semaphore support for PHP -> [http://php.net/manual/fr/ref.sem.php](http://php.net/manual/fr/ref.sem.php) ( in next version, we implement IPC System V for communication between process).

### 3 - How to install SMProtocol ?

For installing SMProtocol is very faster and simply, please clone git via [https://github.com/dlasserre/SMProtocol.git](https://github.com/dlasserre/SMProtocol.git/ "Git repository").
1 - open /etc/environment and add this line 'export APPLICATION_ENV=development' at end of file.
2 - run this command ``` export APPLICATION_ENV=development ```
3 - check if mongod is running.
4 - check the date.timezone is defined in you php.ini
5 - create directory and file : 
    - /var/log/SMProtocol.log (add permissions)
6 - Check all requirements above !
7 - Good luck and have fun :D
8 - Open this file SMProtocol/protocol/tcp/definition.php and read the comments :
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
        /** the port where server will bind **/
        $this->port = 8081;
    }

    public function exception(\Exception $exception)
    {
        /** Log exception here ... */
    }
}
```
9 - update method 
```
<?php 
public function developmentPlugin()
    {
        $_configuration = array(
            /** Plugin list configuration */
            'noSql' => array( // example for mongodb
                'host' => '127.0.0.1',
                'port' => '27017',
                'db' => 'download'
            )
        );
        /** Return */
        return ($_configuration);
    }
?>
```
10 - Move file "/SMProtocol/download" in your /etc/init.d/ and update.rc
11 - Start the service: /etc/init.d/download start
#### More configuration:

*(I advise you not to change)*
>* max_connection: Max client connection by fork (default 5)
>* block_size: Black size for response (default 512 bit)
>* unique_ip: If multiple download from same IP on same file do not accept connection, but I suggest you not turn at True, because in large company all employed have a same public IP...
>* sumaxconn: This parameter is very important, he define the number of simultaneously sockets manage by the kernel ! by default, net.core.somaxconn use, PHP constant SOMAXCONN, i suggest to set at 65535 else the server refused sometime a connection.

**If you want to forward to other service**
>* forward_host: forwarded host
>* forward_port: forwarded port

**socket configuration**
*only for expert user more information follow this link [http://php.net/manual/fr/book.sockets.php](PHP socket "Socket")*
>* socket_domain: type of socket, default AF_INET
>* socket_type: default SOCK_STREAM
>* socket_protocol: SOL_TCP

To run daemon enter this command in your terminal: `` /usr/bin/php SMProtocol.php `` or `` ./SMProtocol ``
Check your process list: 
```
891 pts/2    S+     0:00 /usr/bin/php ./SMProtocol.php
893 pts/2    S+     0:00 /usr/bin/php ./SMProtocol.php

```
*I explain in the third party why you see two process in list.*

### 4 - System operation

SMProtocol is based on php socket and **"fork concept"**, but the very important part is the process manager and socket, SMProtocol use the system call 'socket_select' RTFM :) if you want more information.

Each new connection SMProtocol count the number simultaneous connection in process, if count is superior at max_connection parameter, SMP fork new process with the current connection, clean connection in parent and after accept the new connection.

Each childrens or parent if connection not exceeds 2, used socket select to manage multiple socket by process, the select function is more better than multi-threading (is my personal thinking).

####Please see the workflow bellow:

![Alt text](http://img11.hostingpics.net/pics/305492UntitledDiagram.jpg "Workflow")

After children wipe, no defunct process, the process manage is really optimized :) normally...

### 5 - Database model

Please see this UML bellow:
![Alt text](http://img11.hostingpics.net/pics/187023diagram.png "MCD")

### 6 - Understand SMProtocol log output
SMProtocol return more information in standard output, if you want to trace a problem or bug please check the log and create a ticket in "git-ticket" manager.

**example of output**: 
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
[plugin:noSql] Configuration loaded on 127.0.0.1 port=27017, db=download
[plugin:noSql] MongoDb connection established
[plugin:noSql] Successfully loaded
[tcp] Binding on 127.0.0.1:8081.............
[tcp] Running success
Connection received from 127.0.0.1 on port 45649
[tcp] <<< 338 bytes from <127.0.0.1:45649>
[tcp] File downloaded "Capture.png" 
[tcp] OK 200 Response send
in memcache
[tcp] >>> 159679 bytes to <127.0.0.1:45649@pid:893>: 
[tcp] Connected closed with message "Success"
[tcp] Connection with all clients was terminated and closed, now save stats in database.
[tcp] Garbage Collector: Number of cycle collected < 0 >
[tcp] Save download(s) tracker in database.
[tcp@pid:5695] Save download completed.
```
Is very important to trace log for any problems.