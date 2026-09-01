<?php
// local_uidesign - write endpoint for the Design Studio overlay.
//   POST do=upsert   kind pagetype selector [property] value [label] [enabled]
//   POST do=toggle   id enabled
//   POST do=delete   id
//   POST do=resetall
//   POST do=import   json
// All responses: JSON. Auth: logged-in admin (or local/uidesign:manage) + sesskey.

define('AJAX_SCRIPT', true);
require(__DIR__ . '/../../config.php');

require_once($CFG->dirroot . '/local/uidesign/classes/local/rules.php');

use local_uidesign\local\rules;

require_login(null, false);
$context = context_system::instance();
require_capability('local/uidesign:manage', $context);
require_sesskey();

\core\session\manager::write_close();
header('Content-Type: application/json; charset=utf-8');

$do = optional_param('do', '', PARAM_ALPHA);

try {
    switch ($do) {

        case 'upsert':
            $id = rules::upsert([
                'kind'     => optional_param('kind', '', PARAM_ALPHA),
                'pagetype' => optional_param('pagetype', '*', PARAM_RAW_TRIMMED),
                'selector' => optional_param('selector', '', PARAM_RAW_TRIMMED),
                'property' => optional_param('property', '', PARAM_RAW_TRIMMED),
                'value'    => optional_param('value', '', PARAM_RAW),
                'label'    => optional_param('label', '', PARAM_TEXT),
                'enabled'  => optional_param('enabled', 1, PARAM_BOOL) ? 1 : 0,
            ]);
            echo json_encode(['ok' => true, 'id' => $id, 'rev' => rules::rev()]);
            break;

        case 'toggle':
            rules::set_enabled(required_param('id', PARAM_INT), (bool) optional_param('enabled', 1, PARAM_BOOL));
            echo json_encode(['ok' => true, 'rev' => rules::rev()]);
            break;

        case 'delete':
            rules::delete(required_param('id', PARAM_INT));
            echo json_encode(['ok' => true, 'rev' => rules::rev()]);
            break;

        case 'resetall':
            rules::reset_all();
            echo json_encode(['ok' => true, 'rev' => rules::rev()]);
            break;

        case 'publish':
            rules::publish(optional_param('note', '', PARAM_TEXT));
            echo json_encode(['ok' => true, 'rev' => rules::rev(), 'pending' => rules::pending_count()]);
            break;

        case 'discard':
            rules::discard_drafts();
            echo json_encode(['ok' => true, 'rev' => rules::rev(), 'pending' => rules::pending_count()]);
            break;

        case 'rollback':
            $n = rules::rollback(required_param('id', PARAM_INT));
            echo json_encode(['ok' => true, 'restored' => $n, 'rev' => rules::rev()]);
            break;

        case 'versions':
            echo json_encode(['ok' => true, 'versions' => array_values(rules::versions())]);
            break;

        case 'import':
            $n = rules::import_json(required_param('json', PARAM_RAW));
            echo json_encode(['ok' => true, 'imported' => $n, 'rev' => rules::rev()]);
            break;

        case 'list':
            echo json_encode(['ok' => true, 'rules' => array_values(rules::all()), 'rev' => rules::rev()]);
            break;

        case 'bake':
            $n = rules::bake_scss();
            echo json_encode(['ok' => true, 'baked' => $n, 'rev' => rules::rev()]);
            break;

        case 'findstring':
            $matches = rules::find_string_matches(required_param('text', PARAM_RAW_TRIMMED));
            echo json_encode(['ok' => true, 'matches' => $matches]);
            break;

        case 'overridestring':
            $id = rules::upsert([
                'kind'     => 'lang',
                'selector' => required_param('component', PARAM_RAW_TRIMMED)
                    . '/' . required_param('stringid', PARAM_RAW_TRIMMED),
                'value'    => required_param('value', PARAM_TEXT),
                'label'    => optional_param('label', '', PARAM_TEXT),
            ]);
            echo json_encode(['ok' => true, 'id' => $id, 'rev' => rules::rev()]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'unknown action']);
    }
} catch (\Throwable $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
