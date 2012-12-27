# Copy To HP Cloud
This is a utility CLI and proof of concept for more quickly transferring files from local disk to [HP Cloud Object Storage](https://www.hpcloud.com/products/object-storage).

## Installation
There are a couple prerequisites in PHP and cURL. If you are on a Mac you already have everything you need. On Debian flavored Linux you can use `apt-install php5-cli php5-curl` to install them. If you are on Windows you're out of luck. There's a [bug](https://bugs.php.net/bug.php?id=61141).

Once you have PHP, [download copy-to-hpcloud](http://download.mattfarina.com/copy-to-hpcloud/copy-to-hpcloud) and give it executable permissions (e.g., `chmod +x copy-to-hpcloud`). Execute copy-to-hpcloud for the options and use.

## How does it work?
Transferring lots of smaller files over the Internet can be slow. This is because many tools create a new connection to transfer each file. This means to transfer each file a connection is negotiated and then [TCP slow-start](https://en.wikipedia.org/wiki/Slow-start) kicks in. This does not perform well on short lived connections which is what these are.

Instead, this application opens a connection and sends lots of files over it. This removes the negotiation and start up time making for faster transfers.

_Note, there is still room for improvements in transferring files. For example, with lots of small files this method doesn't come close to saturating a network connection. In addition to fewer connections, transferring files in parallel could speed things up._

## Why is this written in PHP?
I wrote this in PHP because the [HP Cloud PHP bindings](http://hpcloud.github.com/HPCloud-PHP/) already support connection sharing via cURL.

## License
The is licensed under a MIT License.