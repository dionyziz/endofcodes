#!/bin/bash
BASEDIR=$(dirname $0)
cd $BASEDIR
GAMEID=$(php $BASEDIR/run game create)
php $BASEDIR/run game update gameid=$GAMEID finishit=yes
