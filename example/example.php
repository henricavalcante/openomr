<?php
use \OpenOMR\PaperSheet\PaperSheet;
use \OpenOMR\PaperSheet\Field;
use \OpenOMR\PaperSheet\Mark;

	require '../vendor/autoload.php';

	$paper = new PaperSheet(38, 54);

	for ($i = 29; $i <= 35; $i++) {
		$field = new Field('id');
		$field->addMark(new Mark(19, $i, 1));
		$field->addMark(new Mark(20, $i, 2));
		$field->addMark(new Mark(21, $i, 3));
		$field->addMark(new Mark(22, $i, 4));
		$field->addMark(new Mark(23, $i, 5));
		$field->addMark(new Mark(24, $i, 6));
		$field->addMark(new Mark(25, $i, 7));
		$field->addMark(new Mark(26, $i, 8));
		$field->addMark(new Mark(27, $i, 9));
		$field->addMark(new Mark(28, $i, 0));
		$paper->addField($field);
	}

	$fieldId = 1;

	for ($i = 31; $i <= 50; $i++) {
		$field = new Field(str_pad($fieldId, 2, '0', STR_PAD_LEFT));
		$field->addMark(new Mark($i, 3, 'A'));
		$field->addMark(new Mark($i, 4, 'B'));
		$field->addMark(new Mark($i, 5, 'C'));
		$field->addMark(new Mark($i, 6, 'D'));
		$field->addMark(new Mark($i, 7, 'E'));
		$paper->addField($field);

		$fieldId++;
	}

	for ($i = 31; $i <= 50; $i++) {
		$field = new Field(str_pad($fieldId, 2, '0', STR_PAD_LEFT));
		$field->addMark(new Mark($i, 10, 'A'));
		$field->addMark(new Mark($i, 11, 'B'));
		$field->addMark(new Mark($i, 12, 'C'));
		$field->addMark(new Mark($i, 13, 'D'));
		$field->addMark(new Mark($i, 14, 'E'));
		$paper->addField($field);

		$fieldId++;
	}

	for ($i = 31; $i <= 50; $i++) {
		$field = new Field(str_pad($fieldId, 2, '0', STR_PAD_LEFT));
		$field->addMark(new Mark($i, 17, 'A'));
		$field->addMark(new Mark($i, 18, 'B'));
		$field->addMark(new Mark($i, 19, 'C'));
		$field->addMark(new Mark($i, 20, 'D'));
		$field->addMark(new Mark($i, 21, 'E'));
		$paper->addField($field);

		$fieldId++;
	}

	for ($i = 31; $i <= 50; $i++) {
		$field = new Field(str_pad($fieldId, 2, '0', STR_PAD_LEFT));
		$field->addMark(new Mark($i, 24, 'A'));
		$field->addMark(new Mark($i, 25, 'B'));
		$field->addMark(new Mark($i, 26, 'C'));
		$field->addMark(new Mark($i, 27, 'D'));
		$field->addMark(new Mark($i, 28, 'E'));
		$paper->addField($field);

		$fieldId++;
	}

	for ($i = 31; $i <= 50; $i++) {
		$field = new Field(str_pad($fieldId, 2, '0', STR_PAD_LEFT));
		$field->addMark(new Mark($i, 31, 'A'));
		$field->addMark(new Mark($i, 32, 'B'));
		$field->addMark(new Mark($i, 33, 'C'));
		$field->addMark(new Mark($i, 34, 'D'));
		$field->addMark(new Mark($i, 35, 'E'));
		$paper->addField($field);

		$fieldId++;
	}

	$reader = new \OpenOMR\Reader('openomr.jpg', $paper, 4);
	var_dump($reader->getResults());