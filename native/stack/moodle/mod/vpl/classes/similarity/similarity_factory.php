<?php
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
 * Factory class for getting the proper similarity processor based on filename extension.
 *
 * @package mod_vpl
 * @copyright 2022 Juan Carlos Rodríguez-del-Pino
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Juan Carlos Rodríguez-del-Pino <jcrodriguez@dis.ulpgc.es>
 */
namespace mod_vpl\similarity;

use mod_vpl\similarity\similarity_generic;
use mod_vpl\util\languages;

/**
 * Class similarity_factory
 *
 * This class is responsible for creating instances of similarity processors based on file types.
 * It maps file extensions to programming languages and retrieves the appropriate similarity class.
 */
class similarity_factory {
    /**
     * Get all available languages for similarity
     *
     * @return array
     * @codeCoverageIgnore
     */
    protected static function get_available_languages(): array {
        $languages = languages::LANGUAGES2EXTENSION;
        $ext2typearray = [];
        foreach ($languages as $language => $extensions) {
            if (self::get_object($language) !== null) {
                $ext2typearray[] = $language;
            }
        }
        return array_unique(array_values($ext2typearray));
    }

    /**
     * Returns the language name of a file based on its extension.
     *
     * @param string $ext File extension
     * @return string|false Language name or false if not found
     */
    public static function ext2type(string $ext) {
        $language = languages::EXTENSION2LANGUAGE[$ext] ?? false;
        // Check if the language has a similarity class available.
        if ($language !== false && self::get_object($language) === null) {
            return false;
        }
        return $language;
    }

    /**
     * @var string[] $classloaded Saves legaced classes loaded.
     */
    private static array $classloaded = [];

    /**
     * Returns an object of a class derived from similarity_base to process
     * files of a specific language.
     *
     * @param string $langname Language name
     * @return object Object of a class derived from similarity_base
     */
    private static function get_object(string $langname) {
        $similarityclass = self::get_with_similarity_class($langname);

        if (!isset($similarityclass)) {
            $similarityclass = self::get_with_generic($langname);

            if (!isset($similarityclass)) {
                $similarityclass = self::get_with_old_similarity_class($langname);
            }
        }

        return $similarityclass;
    }

    /**
     * Returns an object of a class derived from similarity_base to process a file of a type.
     * This method is used for classes that follow the new naming convention.
     *
     * @param string $langname Language name
     * @return object|null Object of a class derived from similarity_base or null if not found
     */
    private static function get_with_similarity_class(string $langname) {
        $similarityclass = '\mod_vpl\similarity\similarity_' . $langname;

        if (class_exists($similarityclass) === true) {
            return new $similarityclass();
        } else {
            return null;
        }
    }

    /**
     * Returns an object of a class derived from similarity_generic to process a file of a type.
     * This method is used for generic similarity classes that follow the new naming convention.
     *
     * @param string $langname Language name
     * @return object|null Object of a class derived from similarity_generic or null if not found
     */
    private static function get_with_generic(string $langname) {
        $tokenizerrule = dirname(__FILE__) . '/../../similarity/tokenizer_rules/';
        $tokenizerrule .= $langname . '_tokenizer_rules.json';

        if (file_exists($tokenizerrule) === true) {
            return new similarity_generic($langname);
        } else {
            return null;
        }
    }

    /**
     * Returns an object of a class derived from similarity_base to process a file of a type.
     * This method is used for legacy classes that do not follow the new naming convention.
     *
     * @param string $langname Language name
     * @return object|null Object of a class derived from similarity_base or null if not found
     */
    private static function get_with_old_similarity_class(string $langname) {
        if (!isset(self::$classloaded[$langname])) {
            $include = dirname(__FILE__) . '/../../similarity/similarity_';
            $include .= $langname . '.class.php';
            if (!file_exists($include)) {
                return null;
            }
            try {
                require_once($include);
                self::$classloaded[$langname] = true;
                // @codeCoverageIgnoreStart
            } catch (\Throwable $exe) {
                return null;
            }
            // @codeCoverageIgnoreEnd
        }

        $similarityclass = '\vpl_similarity_' . $langname;
        return new $similarityclass();
    }

    /**
     * Get similarity class for passed file name
     *
     * @param string $filename name of a file
     * @return mix
     */
    public static function get(string $filename) {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $langname = self::ext2type($ext);

        if ($langname != false) {
            return self::get_object($langname);
        } else {
            return null;
        }
    }
}
