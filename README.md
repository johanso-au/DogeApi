DogeApi
=======

A few PHP scripts to manage Dogecoin data a bit better on the server side.

populatedb - Calls from the sources, including dogecoind, and populates the DB with the last known valid data - should be a crontab call each minute

api.php - displays the last row of the db, which was populated by the above script.

api.php call result: {"network":{"block":"80199","hashrate":"74.88","difficulty":"1167.3"},"price":{"dogebtc":"187","dogeusdk":"1.528"}}
