<?php

class DataTest extends DataDatabaseTestCase {

	public function testAutoload() {
		$this->assertInstanceOf('Data', new Data);
	}

	public function testImportData() {
		$dataType = new DataType;
		$dataType->dtHandle = 'testing';
		$dataType->dtName = 'Testing';
		$dataType->Insert();
		$xml = new SimpleXMLElement('
			<datatype dtHandle="testing"><data/></datatype>
		');
		$Data = new Data;
		$data = $Data->import($xml->children()->data);
		$this->assertNotNull($data->dID);
	}

	public function testImportDataIncorrectElementName() {
		$xml = new SimpleXMLElement('
			<datatype/>
		');
		$Data = new Data;
		$this->setExpectedException('DataException');
		$Data->import($xml);
	}

	public function testImportDataNoDataTypeHandle() {
		$xml = new SimpleXMLElement('
			<datatype><data/></datatype>
		');
		$Data = new Data;
		$this->setExpectedException('DataException');
		$Data->import($xml->children()->Data);
	}

	public function testImportDataTypeNotFound() {
		$xml = new SimpleXMLElement('
			<datatype dtHandle="randomstring"><data/></datatype>
		');
		$Data = new Data;
		$this->setExpectedException('DataException');
		$Data->import($xml->children()->Data);
	}
}
