function repr($value) {
	if (is_array($value)) {
		$s = array();
		foreach ($value as $item)
			$s[] = repr($item);
		return '['.join(', ', $s).']';
	}
	if (is_string($value)) {
		$s = preg_replace('/\\\\/', '\\\\', $value);
		$s = preg_replace("/'/", '\\\'', $s);
		return "'{$s}'";
	}
	return $value;
	return print_r($value, true);
}

function show_table($table) {
	$lines = array();
	$lines[] = '<table>';
	foreach ($table as $k1 => $value)
		foreach ($value as $k2 => $v) {
		$s = repr($v);
		$lines[] = "<tr><td>{$k1}</td><td>{$k2}</td><td>{$s}</td></tr>";
	}
	echo(join('', $lines));
}
//show_table($transitions);
//die();

$transitions = array('' =>
					 array('{' => 'start_group()',
						   '}' => array('', 'end_group()')));

function add_rule($name, $rule) {
	global $transitions;
	preg_match_all('/\w[\w\d_]*|[\?\*\(\)\|]|\\\\./', $rule, $matches);
	$tokens = $matches[0];
	if (false) {
		header('Content-Type: text/plain');
		print_r($tokens);
		die();
	}
	$state = '';
	$next_state = $name;
	$final_state = '';
	$s = 0;
	$stack = array(); // [$state, $final_state]
	for ($i = 0; $i < count($tokens); ) {
		$token = $tokens[$i++];
		switch ($token) {
		case '(':
			$s++;
			$final_state = "{$name}.{$s}";
			//echo("Save {$final_state}");
			$stack[] = array($state, $final_state);
			break;
		case ')':
			$state = $stack[count($stack)-1][1];
			array_pop($stack);
			break;
		case '|':
			$state = $stack[count($stack)-1][0];
			$final_state = $stack[count($stack)-1][1];
			//echo("Restore {$final_state}");
			break;
		default:
			if ($i+1 == count($tokens) || preg_match('/^[\|\)]$/', $tokens[$i])) $next_state = $final_state;
			//echo("Token {$token}@{$final_state};next={$tokens[$i]}");
			if (preg_match('/\\\\(.)/', $token, $m)) $token = $m[1];
			if ($tokens[$i] == '*') {$i++; $transitions[$state][$token] = array($state); continue;}
			$transitions[$state][$token] = array($next_state);
			$state = $next_state;
			$s++;
			$next_state = "{$name}.{$s}";
		}
	}
}

add_rule('call', '( \) | \] | ident) \( any* \) \{ ( \| any* \| | \} | any )');
//add_rule('function', 'function ident* \(');

show_table($transitions);
die();

// populate this from:
// function: ident? \( any* \)
// term: \( any{arity}* \) { ( \| any* \| 
