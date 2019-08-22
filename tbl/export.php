<?php
require_once('../connect.php');
require_once('../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if($user['role'] != 'admin') {
	$grant = $pdo->prepare("SELECT * FROM grants WHERE user_name=? AND db_name=?") ;
	$grant->execute(array($user['user_name'], $_GET['db']));
	$can = $grant->fetch();
	if(!$can || !$can['select_data']) { ?>
		<h4 style="padding: 30px;">У Вас недостаточно прав</h4>
		<?php exit;
	}
}

function translit($s) {
  $s = (string) $s; // преобразуем в строковое значение
  $s = trim($s); // убираем пробелы в начале и конце строки
  $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
  $s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
  return $s; // возвращаем результат
}


function format($value, $type) {
  switch ($type) {
    case 'tinyint(1)':
      return $value ? 'Да' : 'Нет';
      break;
    case 'date':
      return date('d.m.Y', strtotime($value));
      break;
    
    default:
      return $value;
      break;
  }
}


$describe = "DESCRIBE ".$_GET['db'].'.'.$_GET['tbl'];
$table = $pdo->query($describe)->fetchAll();

$select = "SELECT * FROM ".$_GET['db'].".".$_GET['tbl'];

$params = [];
if(!empty($_GET['filter'])) {
  $where = [];
  foreach ($_GET['filter'] as $key => $value) {
    if($value !== '') {
      if(!is_array($value)) {
        $where[] = $key."=?";
        $params[] = $value;
      } else {
        if(@$value['like']) {
          $where[] = $key." LIKE ?";
          $params[] = '%'.$value['like'].'%';
        }
        if(@$value['from']) {
          $where[] = $key." >= ?";
          $params[] = $value['from'];
        }
        if(@$value['to']) {
          $where[] = $key." <= ?";
          $params[] = $value['to'];
        }
      }
    }
  }
  if(!empty($where))
    $select .= " WHERE ".implode(" AND ", $where);
}

if(!empty($_GET['sort'])) {
  $order = [];
  foreach ($_GET['sort'] as $key => $value) {
    $order[] = $key.' '.$value;
  }
  $select .= " ORDER BY ".implode(',', $order);
}

$stmt = $pdo->prepare($select);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_NUM);

function getColumn($key) {
  $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
  if($key >= count($columns)) {
    return $columns[floor($key/count($columns)) - 1].$columns[$key % count($columns)];
  } else {
    return $columns[$key];
  }
}

$colCount = 0;
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->SetCellValue('A1', $_GET['tbl']);
foreach ($table as $key => $col) {
	$colCount++;
	$sheet->SetCellValue(getColumn($key).'2', $col['Field']);
}
$rowCount = 2;
foreach ($data as $key => $row) {
	$rowCount++;
	foreach (array_values($row) as $key => $col) {
		$sheet->SetCellValue(getColumn($key).$rowCount, format($col, $table[$key]['Type']));
	}
}
$styleArray = array(
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
    ],
    'borders' => [
        'allBorders' => [
        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
    ],
    'font'  => array(
        'bold'  => false,
        'size'  => 12,
        'name'  => 'Times New Roman'
    ));
$sheet->getStyle('A1:'.getColumn($colCount - 1).$rowCount)->applyFromArray($styleArray);
$sheet->getStyle('A1:'.getColumn($colCount - 1).'2')->getFont()->setBold(true);
$sheet->mergeCells('A1:'.getColumn($colCount - 1).'1');
$sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
for($i = 0; $i < $colCount; $i++) {
  	$sheet->getColumnDimension(getColumn($colCount - $i))->setAutoSize(true);
}

if($_GET['format'] == 'pdf') {
    $writer = new Pdf\Mpdf($spreadsheet);
	header('Content-Type: application/pdf');
	@$writer->save('php://output');
	exit;
}
else {
   	$writer = new Xlsx($spreadsheet);
    $file = translit($_GET['tbl']).".xlsx";
    $writer->save($file);
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    // читаем файл и отправляем его пользователю
    readfile($file);
	unlink($file);
}
?>