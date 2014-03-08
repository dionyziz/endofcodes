#include <cstdio>
#include <vector>

struct Creature {
    int id;
    int x;
    int y;
    int hp;
    int userid;
    bool mine;
};

struct Intent {
    int creatureid;
    Action action;
    Direction direction;
};

enum Action {
    NONE, MOVE, ATTACK
};

enum Direction {
    NORTH, EAST, SOUTH, WEST
};

class EndOfCodes {
    private:
        roundFunc;
    public:
        EndOfCodes(getRound) {
            roundFunc = getRound;
        }
        int play() {
            // TODO: networking
        }
};
