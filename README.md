CryptoPonzi
===========
Installation guide:

1. Download your cryptocoin daemon, setup JSON RPC values in your config and add:
```
txindex = 1
```
2. Run your daemon with _-reindex_ and _-server_ arguments
3. Generate your address and private key using vanitygen
4. Fill in the **config.php** file
5. Run **setup.php** script to add your address to the daemon
6. Add **transactions.sql** schema to your database
7. Run **script.php** script from CLI, it should be running in background
8. Install php5-json for the transactions to display properly
9. ???
10. Profit!

Useful links
===========
https://en.bitcoin.it/wiki/Vanitygen - generating custom addresses

https://en.bitcoin.it/wiki/Original_Bitcoin_client/API_calls_list - list of API calls

Screenshoot
===========
![Ponzi](http://i.imgur.com/FOWRlXr.png)

Donate
===========
Wanna donate?

**BTC address:** 1MSkXPRK293dDMD5ds6KqVtyDadDkRyanX

**DOGE address:** DD3mjsvat5Z1W7H8U7mKgfGymTmzwEqB6a
