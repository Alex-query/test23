#!/bin/bash
awk '{arr[$1]+=$2;} END { for (i in arr) print i, arr[i]}'  in.txt | tac > out.txt
