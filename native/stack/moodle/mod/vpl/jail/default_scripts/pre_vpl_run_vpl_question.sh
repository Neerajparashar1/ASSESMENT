#!/bin/bash
# This file is part of VPL Question plugin
# Script for that deals with submitted execution files
# License http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
# Author Astor Bizard <astor.bizard@grenoble-inp.fr>

function vpl_question_deals_with_submitted_execution_files() {
	local qvplfile
	for qvplfile in *
	do
		local file="${qvplfile%_qvpl}"
		if [ -x "$file" ]; then executable=true; else executable=false; fi
		test "$qvplfile" != "$file" && mv "$qvplfile" "$file"
		if $executable; then chmod u+x "$file"; fi
	done
}
vpl_question_deals_with_submitted_execution_files
