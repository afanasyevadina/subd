<?php
$title = 'Таблица "'.$_GET['tbl'].'"';
require_once('header.php'); 

if($user['role'] != 'admin') {
	$grant = $pdo->prepare("SELECT * FROM grants WHERE user_name=? AND db_name=?") ;
	$grant->execute(array($user['user_name'], $_GET['db']));
	$can = $grant->fetch();
	if(!$can || !$can['select_data']) { ?>
		<h4 style="padding: 30px;">У Вас недостаточно прав</h4>
		<?php exit;
	}
}

$describe = "DESCRIBE ".$_GET['db'].'.'.$_GET['tbl'];
if(!$tbl = $pdo->query($describe)) { ?>
	<h4 style="padding: 30px">Данная таблица не существует.</h4>
<?php exit;
}
$table = $tbl->fetchAll();

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
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$per_page = 20;
$total = count($data);
$pages = ceil($total / $per_page);
$data = array_slice($data, $page * $per_page - $per_page, $per_page);

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

?>

<div class="modal fade" id="filter" tabindex="-1" role="dialog" aria-labelledby="filterLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    	<form action="" id="filter_form">
      <div class="modal-header">
        <h5 class="modal-title" id="filterLabel">Фильтр</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="tbl" value="<?=$_GET['tbl']?>">
        <input type="hidden" name="db" value="<?=$_GET['db']?>">
      	<?php foreach($table as $col) { ?>
        <div class="form-group">
        	<p class="font-weight-bold"><?=$col['Field']?></p>
        	<div>
        		<?php if($col['Type'] == 'int(11)' || $col['Type'] == 'float') { ?>
        			<div>От <input type="number" <?=$col['Type'] == 'float' ? 'step="0.01"' : ''?> 
        			name="filter[<?=$col['Field']?>][from]" value="<?=@$_GET['filter'][$col['Field']]['from']?>" 
        			class="form-control" placeholder="От"></div>
        			<div>До <input type="number" <?=$col['Type'] == 'float' ? 'step="0.01"' : ''?> 
        			name="filter[<?=$col['Field']?>][to]" value="<?=@$_GET['filter'][$col['Field']]['to']?>" 
        			class="form-control" placeholder="До"></div>
        		<?php } elseif(substr($col['Type'], 0, 4) == 'enum') {
        			$opts = explode(',', substr($col['Type'], 5, strlen($col['Type'])-6)); ?>
        			<select name="filter[<?=$col['Field']?>]" class="form-control">
        				<option value="">Все значения</option>
        			<?php foreach($opts as $opt) { ?>
        				<option <?=@$_GET['filter'][$col['Field']] == trim($opt, "'") ? 'selected' : ''?>><?=trim($opt, "'")?></option>
        			<?php } ?>
        			</select>
        		<?php } elseif($col['Type'] == 'date') { ?>
        			<div>
        				От <input type="date" name="filter[<?=$col['Field']?>][from]" value="<?=@$_GET['filter'][$col['Field']]['from']?>" class="form-control">
        			</div>
        			<div>
        				До <input type="date" name="filter[<?=$col['Field']?>][to]" value="<?=@$_GET['filter'][$col['Field']]['to']?>" class="form-control">
        			</div>
        		<?php } elseif($col['Type'] == 'tinyint(1)') { ?>
        			<label>
        				<input type="radio" name="filter[<?=$col['Field']?>]" value="1" <?=@$_GET['filter'][$col['Field']] ? 'checked' : ''?>>
        			Да</label>
        			<label style="margin-right: auto; margin-left: 20px;">
        				<input type="radio" name="filter[<?=$col['Field']?>]" value="0" <?=@$_GET['filter'][$col['Field']]==='0' ? 'checked' : ''?>>
        			Нет</label>
        		<?php } else { ?>
        		<input type="text" name="filter[<?=$col['Field']?>][like]" value="<?=@$_GET['filter'][$col['Field']]['like']?>" class="form-control" autocomplete="off">
        		<?php } ?>
        	</div>
        </div>
    	<?php } ?>
      </div>
      <div class="modal-footer">
        <input type="submit" id="showData" value="Показать" class="btn btn-outline-success">
      </div>
    </form>
    </div>
  </div>
</div>

<div class="container-fluid">
	<main class="slider">
		<div class="container-fluid" id="data">
			<div class="row">
				<h3 class="col-lg-7">Таблица "<?=$_GET['tbl']?>"</h3>
				<div class="dropdown col-lg-5">
				  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Просмотр
				  <span class="caret"></span></button>
				  <div class="dropdown-menu">
				    <?php if($can['select_data'] || $user['role'] == 'admin') { ?>
				    	<a class="dropdown-item" href="table.php?tbl=<?=$_GET['tbl']?>&db=<?=$_GET['db']?>">Просмотр</a> <?php } ?>
				    <?php if($can['update_data'] || $user['role'] == 'admin') { ?>
				    	<a class="dropdown-item" href="table_edit.php?tbl=<?=$_GET['tbl']?>&db=<?=$_GET['db']?>">Редактирование</a> <?php } ?>
				    <?php if($can['create_table'] || $user['role'] == 'admin') { ?>
				    	<a class="dropdown-item" href="table_construct.php?tbl=<?=$_GET['tbl']?>&db=<?=$_GET['db']?>">Конструктор</a> <?php } ?>
				  </div>
				<button class="btn btn-outline-info" data-toggle="modal" data-target="#filter">Фильтр</button>
				<button class="btn btn-outline-info" data-toggle="modal" data-target="#export">Экспорт</button>
				</div>
			</div>
			<hr>
			<nav aria-label="Page navigation example">
			  <ul class="pagination">
			    <label class="page-item page-link">
			    	<input type="radio" name="page" form="filter_form" value="1" onchange="this.form.submit()">&laquo;
			    </label>
			    <?php 
			    if($page <=5) { 
				    for($i = 1; $i <= ($pages > 5 ? 5 : $pages); $i++) { ?>
				    	<label class="page-item page-link <?=$i == $page ? 'active' : ''?>"><?=$i?>
				    		<input type="radio" name="page" form="filter_form" value="<?=$i?>" onchange="this.form.submit()">
				    	</label>
				    <?php } 
				} else {
					if($page >= $pages - 5) { 
						for($i = $pages - 5; $i <= $pages; $i++) { ?>
					    	<label class="page-item page-link <?=$i == $page ? 'active' : ''?>"><?=$i?>
					    		<input type="radio" name="page" form="filter_form" value="<?=$i?>" onchange="this.form.submit()">
					    	</label>
					    <?php } 
					} else {
						for($i = $page - 2; $i <= $page + 2; $i++) { ?>
					    	<label class="page-item page-link <?=$i == $page ? 'active' : ''?>"><?=$i?>
					    		<input type="radio" name="page" form="filter_form" value="<?=$i?>" onchange="this.form.submit()">
					    	</label>
					    <?php } 
					}
				} ?>
			    <label class="page-item page-link">
			    	<input type="radio" name="page" form="filter_form" value="<?=$pages?>" onchange="this.form.submit()">&raquo;
			    </label>
			    <label class="page-item page-link">Всего: <?=$total?></label>
			  </ul>
			</nav>
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead>
						<tr>
							<?php
							$pk = '';
							foreach ($table as $key => $col) { ?>
								<th><div  class="sort"><?=$col['Field']?>
									<div>									
										<label class="btn btn-<?=@$_GET['sort'][$col['Field']] == 'asc' ? 'primary' : 'light'?> btn-sm asc">
											&gt;<input onchange="this.form.submit()" form="filter_form" type="radio" name="sort[<?=$col['Field']?>]" value="asc" 
											<?=@$_GET['sort'][$col['Field']] == 'asc' ? 'checked' : ''?>>
										</label>			
										<label class="btn btn-<?=@$_GET['sort'][$col['Field']] == 'desc' ? 'primary' : 'light'?> btn-sm desc">
											&gt;<input onchange="this.form.submit()" form="filter_form" type="radio" name="sort[<?=$col['Field']?>]" value="desc" 
											<?=@$_GET['sort'][$col['Field']] == 'desc' ? 'checked' : ''?>>
										</label>
									</div>
								</div></th>
							<?php } ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($data as $row) { ?>
							<tr>
								<?php foreach (array_values($row) as $key => $col) { ?>
									<td><?=format($col, $table[$key]['Type'])?></td>
								<?php } ?>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</main>
</div>
</main>

<div class="modal fade" id="export" tabindex="-1" role="dialog" aria-labelledby="exportLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exportLabel">Экспорт таблицы</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Выберите формат:</p>
        <div class="form-group">
        	<select form="filter_form" class="form-control" name="format">
        		<option value="xlsx">Excel file (.xlsx)</option>
        		<option value="pdf">Pdf</option>
        	</select>
        </div>
        <input type="hidden" name="tbl" value="<?=$_GET['tbl']?>">
        <input type="hidden" name="db" value="<?=$_GET['db']?>">
      </div>
      <div class="modal-footer">
        <input form="filter_form" type="submit" value="Готово" id="exportData" class="btn btn-outline-success">
      </div>
    </div>
  </div>
</div>


<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/table.js"></script>
<script src="js/walking.js"></script>
</body>
</html>