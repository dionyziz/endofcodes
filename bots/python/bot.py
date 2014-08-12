import json, random, sys
from urlparse import parse_qs
from bottle import route, run, template, request, response, hook

@hook('after_request')
def contentType():
    response.headers['Content-type'] = 'application/json'
    
@route('/bot', method='GET')
def bot():
    return json.dumps({
        'botname': 'pythonbot',
        'version': '0.1.0',
        'username': sys.argv[1]
    })

@route('/game', method='POST')
def game():
    return json.dumps([])

@route('/round', method='POST')
def round():
    vars = parse_qs(request.query_string)
    print(json.dumps(vars))

    map = json.loads(vars['map'])
    game_id, my_id = vars['gameid'], vars['myid']
    round, W, H = vars['round'], vars['W'], vars['H']

    my_creatures = [creature for creature in map if creature['userid'] == myid]
    enemy_creatures = [creature for creature in map if creature['userid'] != myid]

    occupied = set([creature['x'] + creature['y'] * 1j for creature in map])
    enemy_occupied = set([creature['x'] + creature['y'] * 1j for creature in enemy_creatures])

    intents = []
    for my_creature in my_creatures:
        possible_moves = set()
        possible_attacks = set()
        intent = {'creatureid': my_creature['id']}

        location = my_creature['x'] + my_creature['y'] * 1j
        directions = {'north': -1j, 'east': 1, 'south': 1j, 'west': -1}

        for direction, offset in directions.iteritems():
            moved = location + offset
            if 0 <= moved.real < W and 0 <= moved.imag < H:
                if moved not in occupied:
                    possible_moves.add(direction)
                if moved in enemy_occupied:
                    possible_attacks.add(direction)

        if possible_attacks:
            intent['action'] = 'attack'
            intent['direction'] = random.choice(possible_attacks.keys())
        elif possible_moves and random.randint(0, 1) == 0:
            intent['action'] = 'move'
            intent['direction'] = random.choice(possible_moves.keys())

        if 'action' in intent:
            intents.push(intent)

    return json.dumps({'intent': intents})

if len(sys.argv) != 3:
    print('Syntax: python bot.py <username> <port>')
    sys.exit(0)

run(host='localhost', port=sys.argv[2], reloader=True)
