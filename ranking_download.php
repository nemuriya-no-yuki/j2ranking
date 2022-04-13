<?php
	
	// 日付のタイムゾーン変更
	date_default_timezone_set('Asia/Tokyo');

	// 文字列でHTML取得
	$target_url = file_get_contents('URL');
	$target_tag_all = html_entity_decode($target_url);

	// ランキング全体を取得する
	preg_match_all('/<tr class="bg[0-9]">([\S|\s]+?)<\/tr>/s',$target_tag_all, $matches);

	// 件数取得
	$count = count($matches[0])-1;

	for($i=0;$i<=$count;$i++) {

		// 一番最初はヘッダー用のデータにする
		if($i === 0) {
			preg_match_all('/[\s|\S](.+)/',strip_tags($matches[0][$i]),$header_list);
		} else {
			// 2番目以降はデータとして取得
			$ranking_data_list[] = preg_split("/[\s]/", strip_tags($matches[0][$i]));
		}
	}

	// ファイル名を設定
	$file_name = "J2ランキング_".date("YmdHis").".csv";

	// 書き出し専用でファイルを開く
	$open_file = fopen($file_name, 'w');

	// ヘッダーを追加する
	fputcsv($open_file,$header_list[0]);

	// 一行分のデータにする
	foreach($ranking_data_list as $ranking_data_line) {
		// 空白を削除する		
		foreach($ranking_data_line as $key => $ranking_data) {
			// 空白があったら除く
			if(strlen($ranking_data) == 0) {
				unset($ranking_data_line[$key]);
			}
		}
		// ランキングデータを書き込む
		fputcsv($open_file, $ranking_data_line);
	}

	// ファイルを閉じる
	fclose($open_file);

	// ファイルのダウンロード
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename=' . $file_name); 

	// ファイルを出力する
	readfile($file_name);

	// サーバーからファイルを削除する
	unlink($file_name);

?>