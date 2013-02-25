Hey there,

Just a few lines to explain to you what this all is about.

I spent a bit more than 2 years, from 2010 to 2012 trying to develop a site that
would be fair to musicians and great to their fans. The idea was not to make a
lot of money, but to make music something worth it for all - producers and
consumers.

I developed MusXpand full-time and started it as an official business in March
of 2011, in Vancouver.

The premises I based MusXpand upon were to have the fans pay for the music, a
really great price ($15 per year and per artist, to get everything already
released and everything they'd released during the year, then with $15 more the
next year, per artist, they'd get access forever). The artist would be getting
80% of the payments, the 20% remaining would go toward the site's expenses (and
possible staff).

After 1 year and a half within the project, I started understanding that the model
would not work because most fans would not pay for music anymore. The model had
to be changed to something different, either offering site-wide subscriptions
or sponsoring through ads. I opted for the first solution, because I feel ads
are just a visual pollution, and it makes sense fans should reward the musicians
the same way you pay for your food, your gasoline or your entertainment...

After two years, I considered I was unable to carry on this project, too much
disappointed by the fact people were not interested in paying. As I said I had
no interest at all in an ad-sponsored solution, so after two years without a
salary, I eventually desisted of MusXpand.

That's for the little story.

This year, after I found a developer job at Trulioo and had several discussions
with my co-worker Charles (aka "Reiver"), I realized that letting MusXpand die
alone was a total waste of the time I had spent on it, so I decided to go for
an opensource release of the whole code. And that's what you're looking at right
now.

It's important to note the MusXpand sources are released AS-IS. 

What this means is that although MusXpand is a fully functional project (visit
http://www.musxpand.com if you want to give it a try) I had to remove some
information from the code (keys, secrets, etc...) along with some pictures and
logos.

"MusXpand" is also a TRADEMARK I registered in 2010 in the United States and in
Canada. What this means is that whatever you do with this project and code, you
cannot use the name "MUSXPAND" and the associated logo without my written
consent.

Although I loved that brand name and spent time and money in it, I'll be happy
to transfer its ownership to anyone interested, along with the logos and any
associated design for a reasonable fee covering mainly the trademark registration
(for ten years).

Of course, you don't need them to run your own version of MusXpand, however you
call it. But you will have to create the empty resources and adapt whatever is
necessary.

Also, if you're not really a developer, but like the idea and would want to
adapt it to your needs or business idea, I'll be happy to help you customize it
for a compensation that is to be defined. If you have no budget, just forget about
it, I have no intention to work for free anymore on this project. 2 years were
enough.

If you just need some help setting up a site based on the project, providing
your own logo, brandname, domains, etc... feel free to contact me. But, again, 
don't expect me to work for free. You got the code for free, that's already a
great bargain!

So that's it. This is no INSTALL guide unfortunately, and you might have to
guess how things work before your server is up and running, with artists, fans
and media playing. But if you can do, you'll definitely enjoy it!

Cheers,
Philippe Hilger,
Feb. 24, 2013.

                                 ***

Installation hints:
~~~~~~~~~~~~~~~~~~~
- Look for example.com in the files and replace with your own domain

- You may need to tweak the htaccess file a bit and you will need to rename
it .htaccess It will also need to be in your root directory.

- you should also look for keys, secrets and passwords, that have been replaced
by "xxx" in the code. mx_config.php is the main place, but some included SDKs
(like Amazon's or Paypal's) might also need some keys/secrets, so check
everything.

- mediaanalyzer.php, mediauploader.php and sitemapgen.php should run from cron
jobs.

- the APIs and oauth parts are not functional. If you want to offer APIs, you
will need to develop that...

- Good luck! :-)

                                 ***

Development info:
~~~~~~~~~~~~~~~~~
- started in september 2010
- stopped in october 2012
- total lines (including embedded sources): 173,506
- total number of modifications: 28,092
- number of developers: 1
