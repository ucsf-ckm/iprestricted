# IP Restricted Block

Wrap any block of content in your WordPress posts and pages in a shortcode, 
and restrict its visibility by matching the client IP addresses against 
a give white list of IP addresses and/or IP address ranges.

## Installation

Since this plugin is not available for automatic installation, please follow these [Manual Plugin Installation](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation) steps.


## Configuration

This plugin does not require any configuration.

## Usage

Wrap your IP-restricted contents with the `[iprestricted]` shortcode.

Specify which IP addresses are whitelisted via the `whitelist` attribute.

You may specify multiple addresses/ranges as a comma separated list.

Ranges must be provided by declared by their starting and ending IP address, separated by `-`.

If the client's IP address matches any addresses/ranges given in the shortcode,
then the shortcode's content will be displayed.

If the client's IP address fails to be matched against the whitelist, then the content will not be displayed.

### Example

```
[iprestricted whitelist="172.16.42.31,172.16.42.20,172.16.42.5-172.16.43.11"]
Lorem Ipsum.
[/iprestricted]
```

_Lorem Ipsum_ will only be displayed if the client's IP is:

- exactly `172.16.42.31`, _-or-_
- exactly `172.16.42.20`, _-or-_
- between `172.16.42.5` and `172.16.43.11`

## Limitations

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
