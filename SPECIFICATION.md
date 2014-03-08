# End of Codes - Specification

**Dionysis Zindros** <[dionyziz@gmail.com](mailto:dionyziz@gmail.com)>

4 January - 1 March 2014

# Scope

**End of Codes** is a programming game. The project’s goal is to make a game targetting programmers in which each player has to use code to program a strategy for their bot to try and eliminate other players. The game aims to be a competitive programming platform in which programmers can compete for good rankings.

This specification explains the scope of this project and the design details of the application.

 

It will also be used as a base for the development of the project.

# Engineering team

The project will be implemented by an engineering team led by **Dionysis Zindros**, software engineering student at the National Technical University of Athens, and two programmers **Vitalis Salis** and **Dimitris Lamprinos**, software engineering students.

# Workflow

The game consists of two different end-points. The core of the game is the **grader app** which allows players to actually play the game through a RESTful API. The game also exhibits a **web app** in which players can register for their accounts, set up their bots, review their strategy, view the full history of past games, examine opponents’ strategy, and view current rankings.

# Rules of the game

**API version 0.1.0**

## Overview

The game itself is a **turn-based strategy game**. In each game, all players play against all others, in a survival-of-the-fittest manner.  There is one game taking place every 24 hours in which all players participate. The purpose of the game is to be the last-player-standing, while eliminating all other players. 

Each game is associated with a unique **gameid**. The gameid is a positive integer which is kept constant as the game progresses. It is used as an identifier for the game and to distinguish between games.

## Game map

Each game is played on a **2D WxH** grid where W and H are integers. The coordinates of the game are two **zero-based integers** in the form (x, y).

A pair (x, y) is called a **map location** if:

x, y ∈ ℕ

0 ≤ x < W

0 ≤ y < H

A map location (x’, y’) is called a **neighbouring map location** to (x, y) if (x’, y’) is a map location and:

(x’ = x ∧ |y’ - y| = 1) ∨ (y’ = y ∧ |x’ - x| = 1) (NOTE:  ∧ denotes the logical *and* operator and ∨ denotes the logical *or* operator)

## Players

Players play by programming their **bots** to play for their sake; human players do not interact with the game directly. The games are fast-paced, as they are played by computers. This is what disallows human players from playing directly. All players play in each game. We will use **N** to denote the number of players participating in the current game.

The players who play in a game are those who have correctly configured their bots and their bots are available to play.

An **opponent** is any player different from the player in question.

Each player has a unique **userid** which identifies them. This is a positive integer uniquely associated with every player, which does not change from game to game. Other players can utilize the userid to identify the player and deduce their strategy from game to game in order to be able to adapt to it.

## Creatures

Each player has, in their ownership, a set of M **creatures**, a number which is the same for all players when the game begins. The game board is filled with creatures. Each creature belongs to exactly one player and can be **alive** or **dead**.

Each alive creature exists in a specific map location.

Only one alive creature can exist in a particular location. Each particular location is either empty (no creatures are on it), or exactly one creature exists on it. The property that at most one creature may exist on any map location is called **consistency**.

Alive creatures can interact with other creatures.

Each creature is associated with a **creatureid**, a unique positive integer that identifies the creature within the current game. Creatureids may repeat from game to game, but must be unique during a particular game. creatureids allow bots to identify creatures from round to round; a creature retains its creatureid from a round to the next, so that it can be tracked.

## Rounds

The game is round-based. The game begins with round 0, the genesis round. Each round is numbered after a next consequent integer, until the final round, which determines the result of the game. Rounds do not have duration; they are instances of the world in a specific configuration.

## Hit points

Creatures have hit points. A creature’s hit points is a non-negative integer from 0 up to a maximum number **MAX_HP**. Creatures with positive hit points are alive. Creatures with 0 hit points are dead. Once a creature is dead, it cannot be revived. A dead creature’s position does not matter. While alive creatures reserve their position and only one alive creature exists in a certain location, dead creatures do not reserve a position; they can be imagined to exist beyond the game board.

## Grader

The game is overseen by a program called the **grader**. The grader sets the game up, communicates with the bots, determines the configuration of each round, determines the outcome of the game, and tears down the game. Furthermore, the grader is responsible for enforcing game rules. As the grader is the *de facto* rules arbitrator, the source code of the grader is open source and can be used as a reference to the game rules. The *de jure* rules of the documentation are only incidental and derived from the grader’s source code, which is the normative source.

## Initiation

The grader starts the game through the initiation phase. In the initiation phase, the game attributes are decided by the grader. A random W, H, MAX_HP and M are decided. The following inequality must hold in order to ensure there is enough whitespace (set of empty coordinates) in the game board:

WH >= 3NM

The number N is predetermined. It is equal to the number of bots that are readily available to play. Let "α ⇜ A" denote that α is an independently uniformly randomly chosen element from the set A (NOTE:  A uniformly randomly chosen variable from a set is a variable chosen from a set such that all elements of the set have the same probability of being chosen.).

The numbers M, W, H, MAX_HP are determined as follows:

M ⇜ [100, 200[ ∩ ℕ

W ⇜ ]⌈ √(3NM) ⌉, ⌊ √(4NM) ⌋[ ∩ ℕ

H ⇜ ]⌈ √(3NM) ⌉, ⌊ √(4NM) ⌋[ ∩ ℕ

MAX_HP ⇜ [100, 200[ ∩ ℕ

MAX_HP is the same for all creatures. The initial HP for all creatures is set to MAX_HP.

Subsequently, the grader automatically creates the genesis round of the game. This is done by creating M creatures per bot and placing them on the map in random locations as follows. For each player i from 1 to N, for each creature j from 1 to M, the creature j of player i is positioned uniformly at random at an (x, y) coordinate from the coordinates that are not already taken; that position is subsequently marked as taken.

The configuration of the world on the genesis round is predetermined by the grader.

## Visibility

The grader shares the full state of the world with all players at the completion of each round. Therefore, each bot is able to decide what to do based on the current state of the world. There is no fog of war.

## Intents

Upon the completion of every round, and as long as there are more than two creatures belonging to different players alive, the grader informs each player about the current full state of the world and inquires each player about what they want to do. This phase is called the **commit phase**.

Each player decides what they want to do with each of their alive creatures. This signifies an intent for each alive creature, for each player. Players are not able to specify intents for dead creatures. The intent can be one of the following:

* **Move**: The player intents to move the creature from its current location to a new neighbouring location. The move is associated with the desired direction.

* **Attack**: The player intents to have the creature attack an opponent creature in a neighbouring location. The attack is associated with the desired direction.

* **Idle**: The player intents for the creature to remain where it is, doing nothing.

Each player commits to an intent for each of their creatures without being able to see the intents committed by other players. When all players have committed their moves, the game advances to the next round.

## Resolution

Each subsequent round configuration is determined through a process called **resolution**, which is based on the intents committed by each individual player. Resolution follows the following algorithm to advance from round T to round T + 1 after the commit phase.

The resolution algorithm has the purpose of ensuring that the order in which creatures’ intents are examined does not affect the game results. It is desired that the following invariant remains true:

*The resolution of round T + 1 is independent on the order in which creatures intents are satisfied.*

The resolution algorithm also ensures that the map remains consistent.

Here is the resolution algorithm:

1. A new round, T + 1, is created and made to be identical with round T. All creatures retain their locations, owners, identifiers, and hit points from round T to round T + 1.

2. For each creature alive in round T, its intent is examined and, if it is not idle, the following is performed:

    1. If its intent is to attack a neighbouring creature, then:

        1. It is verified that the neighbouring location is within the map bounds. If not, the creature’s intent is set to idle.

        2. It is verified that an alive neighbouring creature existed in round T in the direction indicated by the intent. If not, the creature’s intent is set to idle.

        3. It is verified that the neighbouring creature belonged to an opponent and was alive during round T. If not, the creature’s intent is set to idle.

        4. The creature which is being attacked loses 1 hit point. However, it does not die until the resolution phase is completed. This means that the creature being attacked must still have its intent satisfied.

    2. If its intent is to move to a neighbouring location, then:

        5. It is verified that the neighbouring location is within the map bounds. If not, the creature’s intent is set to idle.

        6. The creature is moved to the new location, regardless of whether a creature already existed in that location during round T. It is also moved to the location regardless of whether another creature moved to that location during round T + 1. This means that, before resolution is completed, a location can be occupied by multiple creatures. A pointer is kept to the old location of the creature in order to be able to move it back if necessary.

3. The creatures whose hit points are less than or equal to 0 are now marked as dead.

4. For every map location in round T + 1 evaluated so far:  

    3. If there are more than 1 creatures in that map location, all these creatures are moved to the location that they had during round T

5. Step 4 is repeated until every map location has at most one creature in it.

Steps 4 and 5 ensure that the map is in consistent condition after the resolution phase is completed. The proof of correctness from resolution follows directly from the inductive assumption that round T is consistent, and step 4a is based on round T locations. Clearly the gensis round is consistent. Step 3 ensures that dead creatures do not take up any space during the resolution phase.

The order in which creatures are examined does not matter, as making a creature move or attack only depends on what happened in the previous round, not the upcoming round. The fact that all creatures that cause map inconsistencies are moved back to their original location ensures that all creatures are treated equally regardless of order.

## Conclusion

After the resolution phase is completed, the game is checked for **winning conditions**. If only creatures owned by a particular user are alive on the map, the particular user is pronounced the winner.

It is possible that noone is the winner of a particular game. This can happen if all creatures remaining in the last round are killed simultaneously.

At this point, the ranking within the game is determined as follows: The player ranking last in the particular game is the player whose last alive creature died first. The player ranking first is the player whose last alive creature died last. Intermediate rankings are taken by players whose last alive creature died at an intermediate round. It is possible that two players are ranked the same in a game if their last creature died in the same round.

A player is deemed **alive** during a round if at least one of their creatures is alive at that round; otherwise the player is **dead** during a particular round. As creatures cannot be revived, players cannot be revived. We will say that a player **dies** during the first round in which he is dead.

The ranking number of each player is the number of alive players during the round he died. The exception is the winner; if we have a winner, the winner is ranked #1; if we have a draw, all players that died in the last round are all ranked #1.

# Bot API

The **Bot API** is a RESTful HTTP API that the bots use to communicate with the grader. Each bot is implemented as an Internet-accesssible HTTP server with an end-point that is used to communicate with the grader. The grader performs HTTP requests to the bot, at its discretion, to inquire about the bot’s intents, and to inform the bot about the world state.

The bots run on the programmers’ machines. This is intentional, allowing programmers to cooperate behind-the-scenes, scale their hardware, or compete using methods other than simply improving their artificial intelligence code.

Each bot has a root end-point which is an http or https URL and can be any valid domain or IP address, including a custom port, and a custom request URI portion. That URL is configured by the players in the web interface of the game and is referred to as the **bot base**. HTTPS URLs must use correct PKI certificates, or will be ignored. If a bot is configured with an https bot base, the grader will only use https and not fallback to http. The current bot base will be denoted as {{botbase}} in this document. The botbase must not end in "/". A “/” will be added at the end of bot base for the request URI portion, so an additional slash is redundant.

Note that it is the grader that is making HTTP requests to each individual bot, not the other way around. The grader is the HTTP client and the bots are the HTTP server.

A bot can misbehave in many ways. We aim to help the programmers by logging the incorrect behavior of bots. This incorrect behavior is then reported in the bot settings page of the web application. Bots that behave incorrectly must be disconnected immediately from the game and all their creatures killed for the particular game. No incorrect responses by the bots is tolerated. The protocol must be followed by the bots to the letter. This is to ensure that the grader is simple and doesn't have to take into account fixing the mistakes of bots; in addition, it will help with forward compatibility, as bots will not rely on specific potentially undefined behavior of the grader.

Before the bot is able to play, the following **sanity checks** are performed by the grader. These events are logged and reported as successful or erroneous:

1. The bot hostname resolves.

2. The bot IP is accessible in the network.

3. The bot is accepting connections to its designated port.

4. The bot is responding to initiation requests with a 200 HTTP OK.

5. The bot is responding with a valid [JSON](https://en.wikipedia.org/wiki/JSON) body.

6. The bot is reporting a botname, version, and username.

7. The username reported by the bot is associated with the user who is using the bot URL.

Each request from the grader to the bot uses either HTTP GET or HTTP POST. The requests are made on the bot base URL and contain an appended request URI which is in the form "resource" where resource is the name of a REST resource. A resource or resource type can be identified by the URL or through the HTTP variables passed; this distinction is made clear in the documented API calls below and exists to ease the development of bots. When performing an HTTP GET request, a resource can be a named item, in which case the individual resource is retrieved, or a generic resource type name, in which the listing of all named resources of the particular type are retrieved. When performing an HTTP POST request, an additional “method” HTTP POST variable is included, which can take the following values:

1. create

2. update

3. delete

When the method is *create*, the request is made to a generic resource type, of which one instance is created; when the method is update, the request is made to an individual resource, which is updated; when the method is delete, the request can be made to a generic resource type, in which case the whole resource is truncated, or, more commonly, to a specific resource, which is subsequently deleted.

All requests are authenticated and authorized by the grader. The authentication is URL-based. If the bot desires to do strong authentication, it must switch to HTTPS. It is recommended that HTTPS is used by all bots.

The grader authenticates itself to the bot using an API key, which is displayed to the bot programmer in the web interface available for bot configuration. The API key is included in all requests and has the name "api_key" and the value displayed in the web interface of the grader. The api key is unique for each bot. The bot can verify that the api key is correct to avoid impostors who may be trying to  deduce one’s strategy beyond the history of available games. The api_key is sent as an HTTP GET variable for GET requests and as an HTTP POST variable for POST requests.

The grader identifies itself using the following User-agent:

user-agent: EndOfCodes/version (grader) <grader@endofcodes.com>

Where version denotes the current API version, currently "0.1.0".

Responses by the bots to the grader are in JSON format. The response JSON is always a dictionary. It can be successful or unsuccessful. An unsuccessful JSON response contains a single key, "error", with a value describing the error produced by the bot, as a string. A successful JSON response contains the JSON dictionary keys associated with the particular request.

The following general errors are logged and reported:

1. The bot stops resolving during the game.

2. The bot stops being accessible through the network during the game.

3. The bot is rejecting connections during the game.

4. The bot responds with a 3xx or 4xx or 5xx HTTP code, or some unknown HTTP code when a 200 OK is expected.

5. The bot returns a non-JSON response when valid JSON is expected.

6. The bot returns valid JSON, but the JSON contains invalid keys or is missing some keys expected in the particular response.

7. The bot returns valid JSON, but indicates that an error has occurred in the JSON using the "error" key.

8. The bot returns valid JSON and keys, but the value is of inappropriate type.

The request may also contain JSON in the POST data. This is true for any non-scalar data structure sent from the grader to the bots. These pieces of JSON are again dictionaries, which contain keys specific to the particular request.

Each bot must be aware of the username of the programmer which it is configured to play for. This helps ensure that the bot is playing for its rightful owner.

## Initiation

Before the initiation phase, the grader hits an HTTP endpoint to determine whether the bot is running. While players should be running their bots constantly, we ensure that dead bots are not included in new games. To do this, the grader sends a GET request to the following:

{{botbase}}/bot

To this, if the bot is ready to play, it must respond with a JSON body that contains the following keys:

1. **botname**: A string, the name of the bot. The botname is up to the programmer.

2. **version**: A string, the version of the bot. The version of the bot is up to the programmer.

3. **username**: A string, the username of the programmer for which the bot is playing

At this stage, the grader will verify that the username associated with the bot base queried matches the username returned by the bot, and register the bot for the game if so. Otherwise, a username mismatch will be reported.

When all bots are queried for availability, the grader performs the initiation phase of the game and sends a create request to the following URL:

{{botbase}}/game

This request contains the following:

1. **gameid**: Integer; a unique positive integer identifier for the game; this integer must not change during the game.

2. **players**: Array; the list of player objects of the players who will play in the game.

3. **W**: Integer.

4. **H**: Integer.

5. **M**: Integer.

6. **MAX_HP**: Integer.

A player object is a dictionary with the following keys:

1. **username**: The username of the player

2. **userid**: A unique integer identifying a player across games; this integer must not change from game to game. It can be used by players to deduce the strategy of opponents across games.

The bot must respond with an empty JSON array to this request. Subsequently, the grader generates the genesis round and requests intent from all bots. If the bot doesn’t respond with an empty JSON, this is considered an error and is reported.

## Round

When each round is completed, the grader collects the round configuration and communicates it to each bot. The bot responds with the intent for each of its creatures for the next round. This request is a create request sent to the following URL:

{{botbase}}/round

The HTTP POST request contains the following variables:

1. **round**: Integer; the number of the round; 0 for genesis.

2. **map**: Array; a list of creature objects, as seen during the round that was just completed.

3. **gameid**: Integer; indicates the gameid of the game as it was communicated during the initiation phase.

4. **myid**: Integer; the userid of the current player. While this is accessible to the user through other means, including it at this stage eases the development of bots.

5. **W**: Integer; same as W in game request.

6. **H**: Integer; same as H in game request.

The map array is an array that contains a list of creatures. Each creature is a dictionary with the following keys:

1. **creatureid**: Integer; the id of the creature; must be unique across creatures within an individual game

2. **userid**: Integer; the userid of the owner of the creature

3. **x**: The x coordinate of the creature’s location

4. **y**: The y coordinate of the creature’s location

5. **hp**: Integer; current hit-points of creature

Dead creatures are included in the list of creatures, but with their hp set to 0. The location of a dead creature is set to the location where it died. However, keep in mind that the particular location may be reused by other creatures.

Note that all creatures of a player may die spontaneously, even without being attacked, if the grader decides that a particular bot is misbehaving.

The bot subsequently responds to the request with a JSON indicating intent and containing the following keys:

1. **intent**: Array; a list of intent objects

An intent object contains the following keys:

1. **creatureid**: Integer; the id of the creature the player wishes to signify an intent for

2. **action**: String; either the string "move" or the string “attack”.

3. **direction**: String, one of "north", “east”, “south”, or “west”.

No entries need to be sent for idle intents. The grader subsequently accepts the commit and performs the resolution phase to evaluate the configuration of the next round and continues making round create requests until the game is completed.

Attempts to manipulate creatures not belonging to their owner are reported as errors. Incorrect desire or direction strings are reported as errors. Attempts to manipulate dead creatures are reported as errors.

After a winner is determined, or we have a draw, a last round create request is sent by the grader, with the last round of the map communicated to the bot. This last request contains alive creatures owned by at most one player; this signifies that the round is the last one.

The bot must respond to the last round with an empty array of intents. However, deviations from this response do not affect the game result, as the game is now finished, so they are not reported or logged.

# Web interface

The game is mainly accessible through a web interface. This web interface consists of the following main pages:

1. Ranking page

2. Game history page

3. Profile page

4. Settings page

The purpose and structure of all these pages is described below.

## Common elements

### Layout

Every page follows a general layout which contains some common elements for all pages.

On the top left of all pages, there is the logo of End of Codes. Clicking on it takes one to the Ranking page.

On the top right of all pages, the user menu appears. If the user is logged out, the text "Log in or Register" appears. “Log in” is a link to the login page and “Register” is a link to the register page. If the user is logged in, the avatar and username of the user and an arrow pointing downwards appear. These are links to the user menu, a drop-down menu which contains a larger view of the user’s avatar, their name, a button to log out labelled "Log out", and a link to the Settings page.

At the bottom of every page appear a link to the development blog labelled "Blog", a link to the project’s GitHub labelled “Contribute”, and a link to the game rules labelled “Rules”.

### E-mails

The web application must sometimes send out e-mails to users. These e-mails are always sent in plaintext format. The "From:" field of e-mails is always set to “[team@endofcodes.com](mailto:team@endofcodes.com)” and these e-mails can be replied-to by the user. Replies are monitored by the support team.

### Notices

Occasionally, some specific pages need to communicate to the user that a specific action has taken place. This is performed through notices. Notices appear at the top of each page, in a box with a highlighted background color. They contain an X button and can be dismissed by clicking it.

### Dates and times

Dates and times are displayed in relative format in the past or in the future. Relative date formats follow the following rules:

1. If the time is within 10 seconds of the current time, the relative time is reported as "now"

2. If the time is in the past then the time is reported as:

    1. "X seconds ago", if the event took place less than a minute ago

    2. "X minutes ago", similarly if the event took place less than an hour ago

    3. "X hours ago", similarly

    4. "X days ago"

    5. "X weeks ago"

    6. "X months ago"

    7. "X years ago"

3. If the time is in the future, then the time is reported similarly to the past, but "in X seconds", “in X minutes” etc.

### Modals

All modal windows appear on top of the existing application, with a black tint covering the rest of the application in the background. An X button at the top right of the window allows dismissing the modal window and canceling the action in question.

### User links

User links are always displayed in distinctive typography. Each user link is displayed with a mini version of their avatar next to their name. Mouse overing a user link displays a user card showing information about that user. The user card contains the user’s avatar, name, ranking, and country. Countries are displayed with their name and flag.

## Login page

The login page allows the user to login. It displays two input boxes, labelled "Username" and “Password” respectively. A button titled “Log in” allows the user to login. If the credentials are correct, the user is logged in and redirected to the Ranking page. Otherwise, they are redirected back to the Login page and an appropriate error message is displayed. If the username did not exist, the error message “Your username does not exist” appears. If the username exists but the password is incorrect, the error message “Your password is incorrect” appears.

The form also has a checkbox named "Remember me" which is checked by default. If the checkbox is checked, then the user is logged in persistently across browser sessions. Otherwise the login expires after the current browser session ends.

Underneath the login form, there are two links, "Forgot password?" and “Register”. The “Forgot password?” link takes the user to the Forgot password page. The Register link takes the user to the Register page.

## Register page

The register page allows the user to register. It contains a list of input boxes with appropriate labels:

1. Username

2. Password

3. Password (repeat)

4. E-mail

The rest of the user information is not requested at this time, to encourage users to register quickly. Users can set additional details in their Settings page.

The register page contains a button labelled "Register". When clicked, the form data is validated as follows:

1. If the username field is blank, the error "Please enter a username" appears

2. If the username is less than 3 characters, the error "Your username must be longer than 3 characters" appears

3. If the username contains characters other than lower-case and upper-case latin or numbers or underscores, the error "Your username must only contain letters, numbers, and _" appears

4. If the password field is blank, the error "Please enter a new password" appears

5. If the password is less than 6 characters long, the error "Your new password must be at least 6 characters long" appears.

6. If the password and password repeat fields do not match, the error "Your two passwords do not match" appears.

7. If the e-mail field is blank, the error "Please enter your e-mail" appears

8. If the e-mail field is not a valid e-mail address, the error "Please enter a valid e-mail address" appears

All errors are displayed next to the respective input field. If multiple errors exist in a particular field, only the first error is displayed. If no errors exist in the form upon submission, a user account is created, the user is logged in and redirected to the Ranking page.

At the bottom of the Register form, there is a link entitled "Already have an account?" which takes the user to the Login page.

## Forgot password page

The Forgot password page contains the title "Reset password". Below, it contains a text input field with the label “Please enter your username” and a button “Reset password”.

Below the Forgot password form, the text "Log in or Create an account" exists. The “Log in” link takes the user to the Log in page, while the “Create an account” takes the user to the Register page.

When the "Reset password" button is clicked, the form is validated as follows:

1. If the username field is blank, the error "Please enter your username" appears

2. If the username doesn’t exist, the error "This username doesn’t exist" appears

Otherwise, the form is submitted and the success message "You will shortly receive an e-mail with instructions to reset your password." appears. An e-mail is then sent to the e-mail associated with the user account submitted. The e-mail text reads:

"Hi {{username}},

You requested to change your End of Codes password. To reset your password, please click the following link:

{{link}}

If you keep having problems, just reply to this e-mail and we’ll be happy to help.

Best,

The End of Codes Team."

{{username}} is replaced with the account username and {{link}} is replaced with a password reset link.

The link in the e-mail expires after 24 hours.

Clicking on the link takes the user to the reset password page, which contains the text "We’re sorry, the forgot password link has expired." if the link has expired. Otherwise, the text “Please enter a new password” is displayed, followed by a form of two password fields labelled “New password” and “New password (repeat)” respectively. A button called “Change password” exists at the bottom.

When the "Change password" button is clicked, the form is validated as follows:

1. If the password field is blank, the error "Please enter a new password" appears.

2. If the password field is less than 6 characters, the error "Your new password must be more than 6 characters" appears.

3. If the password field and the password repeat field do not match, the error "Your two passwords do not match" appears.

If no validation errors exist, then the user’s password is changed to the new password, the user is logged in, and redirected to the Ranking page.

## Ranking page

The Ranking page is the central page of the web interface. It is the landing page when someone is logged out, the landing page when someone is logged in, and the page everyone is redirected to when they complete actions in other pages.

At the top of the page, the text "Next game {{datetime}}" appears, indicating when the next game is taking place. If a game is currently taking place, this text is replaced “Game is currently running”.

If the player is logged out, then the global ratings are shown. Otherwise, global ratings are shown in conjunction with user-specific ratings as described below.

At the top of the ranking page, the last game results are shown. The text "Last game {{datetime}}" appears, where {{datetime}} is the datetime the last game took place. Underneath, the username of the game winner is displayed as a link to their profile next to the word “Winner”.

The datetime next to Last game is clickable and has a small down-arrow next to it. This drops down a calendar which allows the user to select the date the results of whose game they want to view. In that case, the text "Last game {{datetime}}" changes into “Game from {{datetime}}”. In addition, there is a left and a right arrow next to “Game from X” which allows navigating to previous and next games respectively. If the user navigates back to the last game, the text is changed into “Last game {{datetime}}”. When “Last game” is visible, only a left arrow exists for navigating to the previous game. When the first game is displayed, only a right arrow exists for navigating to the next game.

Underneath the game title, there is a link labelled "Go to game map", which navigates the user to the Game history page for the selected game.

Further below, a list of the top 10 ranking users in the selected game appears, with their rank from 1 to 10, and their usernames, country names, flags, avatars, and score. All of them are links to their profiles.

At the top-right of the table with the top 10 users, there is a text labelled "Filter by country" with a drop-box next to it indicating the default choice “All countries”. Choosing a country from the drop-down displays the top 10 ranking within that particular country. The ranking numbers remain global. This choice is remembered when the page is visited again.

If the user is logged in, the table is augmented with their own name and rank and the 10 people around them, with the same details as the top users. The currently logged in user’s entry is highlighted with a yellow background and is shown larger in the table. The entries between the top 10 and the 10 users around the user are indicated with empty horizontal rows of short height and light separating borders. These are clickable at their top section or at their bottom section. When clicked at the top section, they expand to show 10 more rows from the top. When clicked on the bottom section, they expand to show 10 more rows from the bottom. There is a hover effect in both cases, making it clear which part is being expanded. If at any moment there are no entries in between, the table is shown as a continuum. The friends of the user are highlighted in the table with a green background, but they are only highlighted if already displayed. These are true for both the general and the country-filtered rankings.

Underneath the top 10 ranking and the ranking around the current user, there is a graph displayed illustrating the progress of the user during the last 10 days. The graph is labelled "Your recent ranking" and shows a line graph which illustrates the ranking of the user in recent games. The graph's vertical scale illustrates the position of the user, while the horizontal scale indicates time. The vertical scale is constrained to the minimum and maximum ranking the user achieved during these 10 days. The highest data point of the best ranking the user has achieved, while the lowest data point is the worst ranking the user has achieved. At the top right of the table, a drop-down box appears which allows selecting larger periods of time to view the statistics graph. These periods are “Last 10 days”, “Last month”, “Last 6 months”, “Last year” and “All time”, provided such time periods have taken place. "All time" is always displayed.

If the user is logged in, a notice on the ranking page always appears indicating whether their bot is working correctly in green or red. Clicking on this notice takes the user to the bot settings page. The text of the notice is "Your bot is working correctly" if green, or a textual description of the currently ongoing error, if there is an error. These errors are based on the last probe that took place against the bot - not the last game that took place.

## Game history page

The game history page allows a user to view the exact history of a specific game, including all creature moves and attacks. To allow for additional screen real estate, the links at the bottom of the layout are not shown on this page, and the whitespace at the header of the page is limited.

At the top of the game history page, the date during which the game took place is shown. Left and right arrows allow navigating to a previous or next game, if they exist. Clicking on a date allows picking a different game using the same user interface as the ranking page.

On the right of the page, a list of users who participated in the game is shown. Each user is assigned a color, which is used to distinguish their creatures on the game map. Their color is shown next to them, along with their country flag and username.

The main game area shows a portion of the map of the game. The map is separated into columns and rows which are clearly separated using vertical and horizontal lines respectively. Each column and each row is numbered on the game grid. The rows and columns are shown with the bottom-left corner being assigned the coordinates (0, 0) and the top-right corner being assigned the coordinates (W - 1, H - 1). The row numbers appear on the left and the column numbers appear on the right. The game grid expands to take up as much screen space as the user allows, making sure each column and row has equal width and height and there is enough room to clearly indicate each creature on the map.

Each creature appears on the column and row where it exists, provided it is alive. Dead creatures are not displayed. Creatures are shown in circles filled in their owner's color. Mouse overing a creature shows a creature information bubble which contains the player username, country name, and flag, and the creature's id, hit points and location in (x, y) form.

The map shows round 0 by default. At the bottom of the Game history page, a timeline slider appears. The timeline slider allows the user to view different rounds of the game. When the slider is on the left, round 0 is displayed on the map. When the slider is on the right, the last round is displayed on the map. Intermediate rounds are displayed when an intermediate position is selected on the slider.

## Profile page

The profile page is intended for displaying information about a specific user. On the profile page, on the top left, the avatar of the user appears. Next to it, the name of the user appears. Underneath, the flag and country of the user appear. The flag and country are only shown if the user has selected a country on the settings page.

If the viewing user is logged in, if they are viewing their own profile, a link to the Settings page labelled "Edit profile" appears. If they are viewing another user profile and they are their friend, a button labelled “Remove friend” appears, which deletes the friendship from the logged in user to the user whose profile is being viewed. If they are viewing another user profile and they are not their friend, a button labelled “Add friend” appears, which creates a friendship from the logged in user to the user whose profile is being viewed. Friendships are not mutual.

The text "Last game ranking: X" appears underneath, where X indicates the ranking of the player in the last game.

Further down below, the graph with the ranking during the last 10 days of the user is shown. The details of the graph are similar to the graph displayed on the Ranking page for the current user. The user interface elements are the same, allowing the viewer to see statistics over various periods of time.

## Settings page

The settings page allows the user to change their profile settings. 

At the top of the settings page, if the user has set up their bot, a green checkbox appears with the text "Your bot is set up to play the next game". Underneath, the URL of the bot appears, along with a link “Configure bot”. Clicking on the “Configure bot” button takes the user to the Bot settings page. If the user hasn’t set up a bot, then the text “You have not set up your bot yet.” in red appears. A link “Configure bot now” exists underneath, which takes the user to the Bot settings page. If the user has set up their bot incorrectly, or the bot is not functional, the text “You have set up a bot, but it doesn’t seem to work” appears in red. Underneath, the URL of the bot appears, along with a link labelled “Reconfigure bot”, which takes the user to the Bot settings page.

At the top, the avatar of the user is displayed. On the avatar, there is a link labelled "Change profile picture..." Clicking on it allows the user to select an image file, which is set as their profile picture. The image is resized and cropped, maintaining aspect ratio, into a square avatar before being set as the user’s picture.

Underneath, there is a drop-down box with a list of countries and the label "Country". A flag is shown next to each country. At the top of the countries drop-down, a special entry labelled “Prefer not to disclose” exists.

Underneath, there is a calendar which allows the user to set their date of birth (day, month, and year) with the label "Date of Birth". Date of birth is set using a calendar picker, so client-side validation is unnecessary.

Underneath, there is a button labelled "Change password…". When the “Change password” button is clicked, a modal window appears, where 3 input boxes appear labelled “Old password”, “New password” and “New password (repeat)” respectively. A button labelled “Change password” exists underneath. When the “Change password” button is clicked, the form is validated as follows:

1. If the old password field is empty, the error message "Please enter your existing password" appears

2. If the new password field is empty, the error message "Please enter a new password" appears

3. If the new password is less than 6 characters long, the error message "Your new password must be at least 6 characters long" appears

4. If the password field and the repeat password field do not match, the error message "Your two passwords do not match" appears


If no validation errors exist, the password is changed and the modal disappears. A notice appears with the text "Your password was changed successfully".

At the bottom of the settings page, there is a button entitled "Delete account". Clicking it shows a modal window with the text “Account deletion cannot be undone. All your scores will be lost!”. Further below, an input box labelled “Password” appears, along with a button captioned “Delete account”. Clicking on the “Delete account” button deletes the user’s account if the password is correct. Otherwise, the text “Your password is incorrect” is displayed, and the password field is erased and focused.

## Bot settings page

The bot settings page allows the user to configure their bot. At the top, it contains the text:

"To begin playing, you must set up your bot. Start by reading the tutorial."

The "Start by reading the tutorial" is a link to the game rules. Underneath, there is a piece of text containing the bot API key following the label “API key: ”.

Further below, there is an input box with the label "Bot URL:" where the user can enter their Bot URL. A button exists underneath, labelled “Save bot settings”. When clicking it, the form is validated as follows:

1. If the bot URL is blank, the error message "Please enter your bot URL" appears

2. If the bot URL is not a URL, the error message "Please enter a valid HTTP URL" appears

If no validation errors exist, the bot URL is saved. Once the bot URL is saved, the system probes the bot URL to see if it’s up and running and displays a status message below the "Save bot settings" button.

The status message contains the last probing results from the last time the bot was accessed. The bot is probed immediately when the bot settings page is opened, or when the bot URL setting is changed. The probing involves the grader sanity checks specified in the API section of this specification. The text "Your bot is correctly configured" appears with a checkbox in green if the bot is correctly configured. Otherwise, it contains the text “Your bot is incorrectly configured” in red with a cross. Underneath, a list of sanity checks appears, in green or red respectively. Only one red entry appears: The first error. Subsequent red entries are suppressed. For red sanity checks, a link to the appropriate documentation entry is given, in form of a question.

1. If the bot hostname does not resolve, the error message "Your bot hostname is invalid. Did you enter a valid hostname?" appears.

2. If the bot IP is not accessible, the error message "Your bot is unreachable on the network. Did you enter your public IP address?" appears.

3. If the bot refuses connections, the error message "Your bot is refusing connections. Did you port forward correctly?" appears.

4. If the bot accepts connections, but incorrectly responds to initiation requests, the error message "Your bot is running, but not responding to initiation. Did you write code to handle initiation?" appears.

5. If the bot responds with invalid JSON, the error message "Your bot is not sending valid JSON. Did you write code to generate JSON correctly?" appears.

6. If the bot doesn’t respond with the correct keys in the JSON dictionary, the error message "You must set the bot name, version, and your username. Did you build the correct JSON dictionary?" appears.

7. If the bot responds with an incorrect username, the error message "Your bot is not using your username. Did you set your username correctly?" appears.

Underneath the current status, an additional "Last game behavior" section appears. If the bot behaved correctly during the last game, the text “Your bot played correctly in the last game” appears in green and with a checkbox. If the bot performed some illegal action or failed to respond to certain requests in the last game, the first problem the bot encountered is reported in this section, in red. These errors are detailed in the bot communication sections of this specification. These can be sanity check errors during the initiation phase, or problems during the game itself. If these errors caused the bot to be terminated during the game, the additional text " and your creatures were killed by the grader" appears at the end of the error.

# Bot libraries

Libraries to create a bot are provided in the following programming languages:

1. PHP

2. C/C++

3. Javascript (node.js)

4. Ruby

5. Python

Each bot library contains a way to easily set up a web server behind which the application can run, providing both documentation and code for the programmer to get started. All libraries provide the same event-driven interface.

A prototypal, functional bot is included, which behaves in a rule-adhering, but not particularly clever manner. It follows the algorithm below:

1. For each creature owned by the current player:

    1. If an opponent creature is neighbouring the creature in question, attack any neighbouring opponent creature uniformly at random.

    2. Otherwise, uniformly randomly choose to idle or to move.

    3. If you have chosen to move, then move at a valid direction uniformly at random.

The strategy illustrates the basic use of the API, including attacking, moving, and idling.

# Additional requirements

## Security

All pages must be served via HTTPS. Password storage must use modern hashing practices. OWASP top 10 problems must be avoided. The app should be designed with secure-by-default practices. There are no additional security requirements; it is up to the developers to ensure security. Security features such as login, registration, forgot password, and logout flows must be tested additionally for adhering to basic security: One should not be able to login with an empty or wrong password, change password without providing the old password, or recover an account using an expired or incorrect link.

## Compatibility

The web app must be compatible with the latest versions of the following browsers:

1. Chrome

2. Firefox

3. Internet Explorer

4. Safari

5. Opera

Previous versions of browsers need not be supported. It can be assumed that Javascript will always be enabled. No mobile support is needed at this time.
