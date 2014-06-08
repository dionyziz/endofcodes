#!/bin/bash
BASEDIR=$(dirname $0);
php $BASEDIR/run game update gameid=$( php $BASEDIR/run game create ) finishit=yes;
