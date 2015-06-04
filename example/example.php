<?php
	require '../vendor/autoload.php';

	$paper = new \OpenOMR\PaperSheet('openomr.jpg', [38, 54]);

	for ($i = 29; $i <= 35; $i++) {
		$field = new \OpenOMR\Field('id');
		$field->setMark(new \OpenOMR\Mark(19, $i, 1));
		$field->setMark(new \OpenOMR\Mark(20, $i, 2));
		$field->setMark(new \OpenOMR\Mark(21, $i, 3));
		$field->setMark(new \OpenOMR\Mark(22, $i, 4));
		$field->setMark(new \OpenOMR\Mark(23, $i, 5));
		$field->setMark(new \OpenOMR\Mark(24, $i, 6));
		$field->setMark(new \OpenOMR\Mark(25, $i, 7));
		$field->setMark(new \OpenOMR\Mark(26, $i, 8));
		$field->setMark(new \OpenOMR\Mark(27, $i, 9));
		$field->setMark(new \OpenOMR\Mark(28, $i, 0));
		$paper->setField($field);
	}

	$fieldId = 1;

	for ($i = 31; $i <= 50; $i++) {
		$field = new \OpenOMR\Field(str_pad($fieldId, 2, '0', STR_PAD_LEFT));
		$field->setMark(new \OpenOMR\Mark($i, 3, 'A'));
		$field->setMark(new \OpenOMR\Mark($i, 4, 'B'));
		$field->setMark(new \OpenOMR\Mark($i, 5, 'C'));
		$field->setMark(new \OpenOMR\Mark($i, 6, 'D'));
		$field->setMark(new \OpenOMR\Mark($i, 7, 'E'));
		$paper->setField($field);

		$fieldId++;
	}

	for ($i = 31; $i <= 50; $i++) {
		$field = new \OpenOMR\Field(str_pad($fieldId, 2, '0', STR_PAD_LEFT));
		$field->setMark(new \OpenOMR\Mark($i, 10, 'A'));
		$field->setMark(new \OpenOMR\Mark($i, 11, 'B'));
		$field->setMark(new \OpenOMR\Mark($i, 12, 'C'));
		$field->setMark(new \OpenOMR\Mark($i, 13, 'D'));
		$field->setMark(new \OpenOMR\Mark($i, 14, 'E'));
		$paper->setField($field);

		$fieldId++;
	}

	for ($i = 31; $i <= 50; $i++) {
		$field = new \OpenOMR\Field(str_pad($fieldId, 2, '0', STR_PAD_LEFT));
		$field->setMark(new \OpenOMR\Mark($i, 17, 'A'));
		$field->setMark(new \OpenOMR\Mark($i, 18, 'B'));
		$field->setMark(new \OpenOMR\Mark($i, 19, 'C'));
		$field->setMark(new \OpenOMR\Mark($i, 20, 'D'));
		$field->setMark(new \OpenOMR\Mark($i, 21, 'E'));
		$paper->setField($field);

		$fieldId++;
	}


	for ($i = 31; $i <= 50; $i++) {
		$field = new \OpenOMR\Field(str_pad($fieldId, 2, '0', STR_PAD_LEFT));
		$field->setMark(new \OpenOMR\Mark($i, 24, 'A'));
		$field->setMark(new \OpenOMR\Mark($i, 25, 'B'));
		$field->setMark(new \OpenOMR\Mark($i, 26, 'C'));
		$field->setMark(new \OpenOMR\Mark($i, 27, 'D'));
		$field->setMark(new \OpenOMR\Mark($i, 28, 'E'));
		$paper->setField($field);

		$fieldId++;
	}

	for ($i = 31; $i <= 50; $i++) {
		$field = new \OpenOMR\Field(str_pad($fieldId, 2, '0', STR_PAD_LEFT));
		$field->setMark(new \OpenOMR\Mark($i, 31, 'A'));
		$field->setMark(new \OpenOMR\Mark($i, 32, 'B'));
		$field->setMark(new \OpenOMR\Mark($i, 33, 'C'));
		$field->setMark(new \OpenOMR\Mark($i, 34, 'D'));
		$field->setMark(new \OpenOMR\Mark($i, 35, 'E'));
		$paper->setField($field);

		$fieldId++;
	}

	$reader = new \OpenOMR\Reader($paper);
	var_dump($reader->getResults());