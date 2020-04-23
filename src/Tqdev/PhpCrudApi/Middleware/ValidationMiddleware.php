<?php

namespace Tqdev\PhpCrudApi\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tqdev\PhpCrudApi\Column\ReflectionService;
use Tqdev\PhpCrudApi\Column\Reflection\ReflectedTable;
<<<<<<< HEAD
=======
use Tqdev\PhpCrudApi\Column\Reflection\ReflectedColumn;
>>>>>>> 3605953a927842c88dfbc873d105c964bec3011d
use Tqdev\PhpCrudApi\Controller\Responder;
use Tqdev\PhpCrudApi\Middleware\Base\Middleware;
use Tqdev\PhpCrudApi\Middleware\Router\Router;
use Tqdev\PhpCrudApi\Record\ErrorCode;
use Tqdev\PhpCrudApi\RequestUtils;

<<<<<<< HEAD
class ValidationMiddleware extends Middleware {
	private $reflection;
	private $typesToValidate;

	public function __construct(Router $router, Responder $responder, array $properties, ReflectionService $reflection) {
		parent::__construct($router, $responder, $properties);
		$this->reflection = $reflection;
		$typesStr = $this->getProperty('types', 'all');
		if (is_null($typesStr)) {
			$typesStr = 'all';
		}
		if (strlen($typesStr) == 0) {
			$typesStr = 'none';
		}
		$this->typesToValidate = explode(',', $typesStr);
		if (is_null($this->typesToValidate) || count($this->typesToValidate) == 0) {
			$this->typesToValidate = ['all'];
		}
	}

	private function callHandler($handler, $record, string $operation, ReflectedTable $table) /*: ResponseInterface?*/ {
=======
class ValidationMiddleware extends Middleware
{
	private $reflection;

	public function __construct(Router $router, Responder $responder, array $properties, ReflectionService $reflection)
	{
		parent::__construct($router, $responder, $properties);
		$this->reflection = $reflection;
	}

	private function callHandler($handler, $record, string $operation, ReflectedTable $table) /*: ResponseInterface?*/
	{
>>>>>>> 3605953a927842c88dfbc873d105c964bec3011d
		$context = (array) $record;
		$details = array();
		$tableName = $table->getName();
		foreach ($context as $columnName => $value) {
			if ($table->hasColumn($columnName)) {
				$column = $table->getColumn($columnName);
				$valid = call_user_func($handler, $operation, $tableName, $column->serialize(), $value, $context);
<<<<<<< HEAD
				if ($valid || $valid == '') {
					$valid = $this->validateType($column->serialize(), $value);
=======
				if ($valid === true || $valid === '') {
					$valid = $this->validateType($column, $value);
>>>>>>> 3605953a927842c88dfbc873d105c964bec3011d
				}
				if ($valid !== true && $valid !== '') {
					$details[$columnName] = $valid;
				}
			}
		}
		if (count($details) > 0) {
			return $this->responder->error(ErrorCode::INPUT_VALIDATION_FAILED, $tableName, $details);
		}
		return null;
	}

<<<<<<< HEAD
	private function validateType($column, $value) {
		if ($this->typesToValidate[0] == 'none') {
			return (true);
		}
		if ($this->typesToValidate[0] != 'all') {
			if (!in_array($column['type'], $this->typesToValidate)) {
				return (true);
			}
		}
		if (is_null($value)) {
			return ($column["nullable"] ? true : "cannot be null");
		}
		switch ($column['type']) {
		case 'integer':
			if (!is_numeric($value)) {
				return ('must be numeric');
			}

			if (strlen($value) > 20) {
				return ('exceeds range');
			}

			break;
		case 'bigint':
			if (!is_numeric($value)) {
				return ('must be numeric');
			}

			if (strlen($value) > 20) {
				return ('exceeds range');
			}

			break;
		case 'varchar':
			if (strlen($value) > $column['length']) {
				return ('too long');
			}

			break;
		case 'decimal':
			if (!is_float($value) && !is_numeric($value)) {
				return ('not a float');
			}

			break;
		case 'float':
			if (!is_float($value) && !is_numeric($value)) {
				return ('not a float');
			}

			break;
		case 'double':
			if (!is_float($value) && !is_numeric($value)) {
				return ('not a float');
			}

			break;
		case 'boolean':
			if ($value != 0 && $value != 1) {
				return ('not a valid boolean');
			}

			break;
		case 'date':
			$date_array = explode('-', $value);
			if (count($date_array) != 3) {
				return ('invalid date format use yyyy-mm-dd');
			}

			if (!@checkdate($date_array[1], $date_array[2], $date_array[0])) {
				return ('not a valid date');
			}

			break;
		case 'time':
			$time_array = explode(':', $value);
			if (count($time_array) != 3) {
				return ('invalid time format use hh:mm:ss');
			}

			foreach ($time_array as $t) {
				if (!is_numeric($t)) {
					return ('non-numeric time value');
				}
			}

			if ($time_array[1] < 0 || $time_array[2] < 0 || $time_array[0] < -838 || $time_array[1] > 59 || $time_array[2] > 59 || $time_array[0] > 838) {
				return ('not a valid time');
			}

			break;
		case 'timestamp':
			$split_timestamp = explode(' ', $value);
			if (count($split_timestamp) != 2) {
				return ('invalid timestamp format use yyyy-mm-dd hh:mm:ss');
			}

			$date_array = explode('-', $split_timestamp[0]);
			if (count($date_array) != 3) {
				return ('invalid date format use yyyy-mm-dd');
			}

			if (!@checkdate($date_array[1], $date_array[2], $date_array[0])) {
				return ('not a valid date');
			}

			$time_array = explode(':', $split_timestamp[1]);
			if (count($time_array) != 3) {
				return ('invalid time format use hh:mm:ss');
			}

			foreach ($time_array as $t) {
				if (!is_numeric($t)) {
					return ('non-numeric time value');
				}
			}

			if ($time_array[1] < 0 || $time_array[2] < 0 || $time_array[0] < 0 || $time_array[1] > 59 || $time_array[2] > 59 || $time_array[0] > 23) {
				return ('not a valid time');
			}

			break;
		case 'clob':
			break;
		case 'blob':
			break;
		case 'varbinary':
			if (((strlen($value) * 3 / 4) - substr_count(substr($value, -2), '=')) > $column['length']) {
				return ('too long');
			}

			break;
		case 'geometry':
			break;
=======
	private function validateType(ReflectedColumn $column, $value)
	{
		$types = $this->getArrayProperty('types', 'all');
		if (in_array('all', $types) || in_array($column->getType(), $types)) {
			if (is_null($value)) {
				return ($column->getNullable() ? true : "cannot be null");
			}
			if (is_string($value)) {
				// check for whitespace
				switch ($column->getType()) {
					case 'varchar':
					case 'clob':
						break;
					default:
						if (strlen(trim($value)) != strlen($value)) {
							return 'illegal whitespace';
						}
						break;
				}
				// try to parse
				switch ($column->getType()) {
					case 'integer':
					case 'bigint':
						if (
							filter_var($value, FILTER_SANITIZE_NUMBER_INT) !== $value ||
							filter_var($value, FILTER_VALIDATE_INT) === false
						) {
							return 'invalid integer';
						}
						break;
					case 'varchar':
						if (mb_strlen($value, 'UTF-8') > $column->getLength()) {
							return 'string too long';
						}
						break;
					case 'decimal':
						if (!is_numeric($value)) {
							return 'invalid decimal';
						}
						break;
					case 'float':
					case 'double':
						if (
							filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT) !== $value ||
							filter_var($value, FILTER_VALIDATE_FLOAT) === false
						) {
							return 'invalid float';
						}
						break;
					case 'boolean':
						if (
							filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === null
						) {
							return 'invalid boolean';
						}
						break;
					case 'date':
						if (date_create_from_format('Y-m-d', $value) === false) {
							return 'invalid date';
						}
						break;
					case 'time':
						if (date_create_from_format('H:i:s', $value) === false) {
							return 'invalid time';
						}
						break;
					case 'timestamp':
						if (date_create_from_format('Y-m-d H:i:s', $value) === false) {
							return 'invalid timestamp';
						}
						break;
					case 'clob':
						// no checks needed
						break;
					case 'blob':
					case 'varbinary':
						if (base64_decode($value, true) === false) {
							return 'invalid base64';
						}
						break;
					case 'geometry':
						// no checks yet
						break;
				}
			} else { // check non-string types
				switch ($column->getType()) {
					case 'integer':
					case 'bigint':
						if (!is_int($value)) {
							return 'invalid integer';
						}
						break;
					case 'decimal':
					case 'float':
					case 'double':
						if (!is_float($value) && !is_int($value)) {
							return 'invalid float';
						}
						break;
					case 'boolean':
						if (!(is_int($value) && ($value === 1 || $value === 0)) && !is_bool($value)) {
							return 'invalid boolean';
						}
						break;
					default:
						return 'invalid ' . $column->getType();
				}
			}
			// extra checks
			switch ($column->getType()) {
				case 'integer': // 4 byte signed
					$value = filter_var($value, FILTER_VALIDATE_INT);
					if ($value > 2147483647 || $value < -2147483648) {
						return 'invalid integer';
					}
					break;
				case 'decimal':
					$value = "$value";
					if (strpos($value, '.') !== false) {
						list($whole, $decimals) = explode('.', $value, 2);
					} else {
						list($whole, $decimals) = array($value, '');
					}
					if (strlen($whole) > 0 && !ctype_digit($whole)) {
						return 'invalid decimal';
					}
					if (strlen($decimals) > 0 && !ctype_digit($decimals)) {
						return 'invalid decimal';
					}
					if (strlen($whole) > $column->getPrecision() - $column->getScale()) {
						return 'decimal too large';
					}
					if (strlen($decimals) > $column->getScale()) {
						return 'decimal too precise';
					}
					break;
				case 'varbinary':
					if (strlen(base64_decode($value)) > $column->getLength()) {
						return 'string too long';
					}
					break;
			}
>>>>>>> 3605953a927842c88dfbc873d105c964bec3011d
		}
		return (true);
	}

<<<<<<< HEAD
	public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface{
=======
	public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
	{
>>>>>>> 3605953a927842c88dfbc873d105c964bec3011d
		$operation = RequestUtils::getOperation($request);
		if (in_array($operation, ['create', 'update', 'increment'])) {
			$tableName = RequestUtils::getPathSegment($request, 2);
			if ($this->reflection->hasTable($tableName)) {
				$record = $request->getParsedBody();
				if ($record !== null) {
					$handler = $this->getProperty('handler', '');
					if ($handler !== '') {
						$table = $this->reflection->getTable($tableName);
						if (is_array($record)) {
							foreach ($record as $r) {
								$response = $this->callHandler($handler, $r, $operation, $table);
								if ($response !== null) {
									return $response;
								}
							}
						} else {
							$response = $this->callHandler($handler, $record, $operation, $table);
							if ($response !== null) {
								return $response;
							}
						}
					}
				}
			}
		}
		return $next->handle($request);
	}
}
