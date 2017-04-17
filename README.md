# IP Restricted Block

Wrap any block of content in your WordPress posts and pages in a shortcode to restrict its visibility by matching the 
client's IP address against specified white- and blacklists of IP addresses.

## Installation

Since this plugin is not available for automatic installation, please follow these [Manual Plugin Installation](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation) steps.


## Configuration

This plugin does not require any configuration.

## Usage

Wrap your IP-restricted contents with the `[iprestricted]` shortcode.

Specify which IP addresses are whitelisted via the `whitelist` attribute, 
and which which IP addresses are blacklisted via the `blacklist` attribute.

### Syntax for white- and blacklists

You may specify multiple addresses/ranges as a comma separated list.

Ranges must be provided by declared by their starting and ending IP address, separated by `-`.

### Restriction rules

1. The shortcode's content will be displayed if the client IP matches any IP on the given whitelist.
2. The shortcode's content will not be displayed if the client IP matches any IP on the given blacklist.
3. Blacklist matches take precedence ove whitelist matches, in case the client IP matches both.
4. If neither whitelist or blacklist values are provided, then the content will be displayed.

### Examples

#### Whitelisting IPs

```
[iprestricted whitelist="172.16.42.31,172.16.42.20,172.16.42.5-172.16.43.11"]
Lorem Ipsum.
[/iprestricted]
```

_Lorem Ipsum_ will only be displayed if the client's IP is:

- exactly `172.16.42.31`, _-or-_
- exactly `172.16.42.20`, _-or-_
- between `172.16.42.5` and `172.16.43.11`

#### Blacklisting IPs

```
[iprestricted blacklist="172.16.42.5-172.16.43.11"]
Lorem Ipsum.
[/iprestricted]
```

_Lorem Ipsum_ will only be displayed if the client's IP does not fall between `172.16.42.5` and `172.16.43.11`.L

#### Putting it all together

```
[iprestricted whitelist="172.16.42.5-172.16.43.11" blacklist="172.16.43.1"]
Lorem Ipsum.
[/iprestricted]
``` 

_Lorem Ipsum_ will only be displayed if the client's IP falls between `172.16.42.5` and `172.16.43.11`, with the exception of `172.16.43.1`.


#### Multiple IP restricted blocks

Use multiple blocks to express mutually exclusive conditions for restricting content visibility.

```
[iprestricted whitelist="127.0.0.1"]
Show this only on localhost.
[/iprestricted]

[iprestricted blacklist="127.0.0.1"]
Hide this on localhost
[/iprestricted]
```

## Limitations
- if your web server has a reverse proxy that's caching pages in front of it, this will not work.
- only works with IPv4 addresses.
- server-side, the client IP must be accessible via the `REMOTE_ADDR`, `HTTP_X_FORWARDED_FOR` or `HTTP_CLIENT_IP` server variables. See this [StackOverflow post](http://stackoverflow.com/questions/3003145/how-to-get-the-client-ip-address-in-php) for a discussion of this approach.
- only works [reliably](http://php.net/manual/en/function.ip2long.php#113080) on servers running 64-bit operating systems.

## Other considerations

**Do not use this plugin to hide sensitive information**. 

Whenever this plugin gets disabled or uninstalled, 
any of its shortcodes remaining in your post and pages will be printed out as-is, _including its wrapped contents_.

## Copyright and License

Copyright (c) 2017 The Regents of the University of California

This is Open Source Software, published under the MIT license.
