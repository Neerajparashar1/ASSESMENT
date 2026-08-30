// This file is part of VPL for Moodle - http://vpl.dis.ulpgc.es/
//
// VPL for Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// VPL for Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with VPL for Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Terminal themes
 *
 * @copyright 2026 Juan Carlos Rodríguez-del-Pino
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Juan Carlos Rodríguez-del-Pino <jcrodriguez@dis.ulpgc.es>
 */

const themes = {
    "Default": {
        name: "Default",
        background: "#000000",
        foreground: "#ffffff",
        cursor: "#ffffff",
        selectionBackground: "#555555"
    },
    "Reverse": {
        name: "Reverse",
        background: "#ffffff",
        foreground: "#000000",
        cursor: "#000000",
        selectionBackground: "#cccccc"
    },
    "Navy yellow": {
        name: "Navy yellow",
        background: "#000080",
        foreground: "#ffff00",
        cursor: "#ffff00",
        selectionBackground: "#cccccc"
    },
    "Blue white": {
        name: "Blue white",
        background: "#000080",
        foreground: "#ffffff",
        cursor: "#ffffff",
        selectionBackground: "#cccccc"
    },
    "Red": {
        name: "Red",
        background: "#00ffff",
        foreground: "#ff0000",
        cursor: "#ff0000",
        selectionBackground: "#cccccc"
    },
    "Dracula": {
        name: "Dracula",
        background: "#282a36",
        foreground: "#f8f8f2",
        cursor: "#f8f8f2",
    },
    "Solarized Dark": {
        name: "Solarized Dark",
        background: "#002b36",
        foreground: "#839496",
        cursor: "#93a1a1",
    },
    "Solarized Light": {
        name: "Solarized Light",
        background: "#fdf6e3",
        foreground: "#657b83",
        cursor: "#586e75"
    },
    "Monokai": {
        name: "Monokai",
        background: "#272822",
        foreground: "#f8f8f2",
        cursor: "#f8f8f0",
    },
    "Nord": {
        name: "Nord",
        background: "#2e3440",
        foreground: "#d8dee9",
        cursor: "#d8dee9",
    },
    "Gruvbox Dark": {
        name: "Gruvbox Dark",
        background: "#282828",
        foreground: "#ebdbb2",
        cursor: "#ebdbb2",
    },
    "Gruvbox Light": {
        name: "Gruvbox Light",
        background: "#fbf1c7",
        foreground: "#3c3836",
        cursor: "#3c3836"
    },
    "Matrix": {
        name: "Matrix",
        background: "#000000",
        foreground: "#00ff41",
        cursor: "#00ff41",
    },
    "Cyberpunk": {
        name: "Cyberpunk",
        background: "#0f0f1a",
        foreground: "#f72585",
        cursor: "#4cc9f0",
    },
    "Pastel": {
        name: "Pastel",
        background: "#fdfdff",
        foreground: "#5c677d",
        cursor: "#5c677d",
    },
    "Paper": {
        name: "Paper",
        background: "#f5f5f5",
        foreground: "#2e2e2e",
        cursor: "#2e2e2e"
    },
    "Forest": {
        name: "Forest",
        background: "#0b3d2e",
        foreground: "#a7f3d0",
        cursor: "#a7f3d0"
    },
    "Fire": {
        name: "Fire",
        background: "#1a0000",
        foreground: "#ff4500",
        cursor: "#ff6347"
    },
    "Ice": {
        name: "Ice",
        background: "#e0f7fa",
        foreground: "#006064",
        cursor: "#006064"
    }
};

/**
 * Get the list of terminal themes.
 *
 * @returns {Object} An object containing terminal theme objects.
 */
function getThemes() {
    return themes;
}

export var VPLTerminalThemes = {
    init: getThemes,
    getThemes: getThemes,
};

