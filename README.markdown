# BearsLoveHoney_MultipartAlternativeEmail #

## Description ##

Tested with Magento 1.5.1.0, Community Edition.

This project aims to make Magento emails less spammy and more likely to reach a recipients inbox.  
Magento only provides the ability to set either a HTML email or Plain Text email, this module allows [Multipart messages](http://en.wikipedia.org/wiki/MIME#Multipart_messages) to be sent that include both types.

> Most commonly, multipart/alternative is used for e-mail with two parts, one plain text (text/plain) and one HTML (text/html). The plain text part provides backwards compatibility while the HTML part allows use of formatting and hyperlinks. Most e-mail clients offer a user option to prefer plain text over HTML; this is an example of how local factors may affect how an application chooses which "best" part of the message to display.

> &mdash; <cite>[multipart/alternative](http://en.wikipedia.org/wiki/MIME#Alternative)</cite>

## Installation ##

Copy the files into their respective directories within your Magento root.

As with many extensions that alter the admin you will need to log-out and then log-in to see the configuration settings.

## Configuration ##

There is only one configuration option under System->Configuration->Multipart Alternative Email Config.  
This can be used to override the default Boundary String `--EMAIL_BOUNDARY--` that is used to define the end of the HTML part and the beginning of the Plain Text part of the email.

To use, insert the Boundary String (defaults to `--EMAIL_BOUNDARY--`) at the end of your HTML transactional email templates, then include a plain text version of the email.

## To Do ##

* Include plain text templates for addresses and items
* Include default transactional email templates with plain text versions