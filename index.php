<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


function pagination_pgdn_autoload()
{
	mso_hook_add('pagination', 'pagination_pgdn_go', 10);
}


function pagination_pgdn_go($r = array())
{
	global $MSO;

	$r_orig = $r;

	if (!$r) return $r;
	if ( !isset($r['maxcount']) ) return $r;
	if ( !isset($r['limit']) ) return $r; // нужно указать сколько записей выводить
	if ( !isset($r['type']) )  $r['type'] = false; // можно задать свой тип

	if ( !isset($r['next_url']) ) $r['next_url'] = 'next';

	$options = mso_get_option('plugin_pagination_pgdn', 'plugins', array() ); // получаем опции

	if ( !isset($r['range']) )
		$r['range'] = isset($options['range']) ? (int) $options['range'] : 3;

	if ( !isset($r['sep']) )
		$r['sep'] = isset($options['sep']) ? $options['sep'] : ' ';

	if ( !isset($r['sep2']) )
		$r['sep2'] = isset($options['sep2']) ? $options['sep2'] : ' ';


	if ( !isset($r['format']) )
	{
		// $r['format'] =
		$r['format'][] = isset($options['format_first']) ? $options['format_first'] : '&lt;&lt;';
		$r['format'][] = isset($options['format_prev']) ? $options['format_prev'] : '&lt;';
		$r['format'][] = isset($options['format_next']) ? $options['format_next'] : '&gt;';
		$r['format'][] = isset($options['format_last']) ? $options['format_last'] : '&gt;&gt;';
	}

	# текущая пагинация вычисляется по адресу url
	# должно быть /next/6 - номер страницы

	$current_paged = mso_current_paged($r['next_url']);

	if ($current_paged > $r['maxcount']) $current_paged = $r['maxcount'];

	if ($r['type'] !== false)
		$type = $r['type'];
	else
		$type = $MSO->data['type'];

	// текущий адрес
	$cur_url = mso_current_url(true);

	// в текущем адресе нужно исключить пагинацию next
	if (preg_match("!/" . $r['next_url'] . "/!is", $cur_url, $matches, PREG_OFFSET_CAPTURE))
	{
		$cur_url = substr($cur_url, 0, $matches[0][1]);
	}

	if ($type == 'home' and $current_paged == 1) $cur_url = $cur_url . 'home';

	// pr($cur_url);

	if ($type == 'home')
		$home_url = getinfo('site_url');
	else
		$home_url = $cur_url;

	$out = _pagination_pgdn( $r['maxcount'],
						$current_paged,
						$cur_url . '/' . $r['next_url'] . '/',
						$r['range'],
						$cur_url,
						'',
						$r['sep'],
						$home_url,
						$r['sep2']
						);

	if ($out)
	{
		$out = str_replace(
				array('%FIRST%', '%PREV%', '%NEXT%', '%LAST%'),
				$r['format'],
				$out);

		echo "\r\n".'<script type="text/javascript">
                    $(document).keydown(function(e){  // обработчик на каждую нажатую кнопку
                        var next = $(".pagination-next");  // находим <a> "туда"
                        if (next && (e.which == 34 || e.which == 32 || e.which == 39) && ($(window).scrollTop() + $(window).height() == $(document).height())) {
                            // "туда" найден && код клавиши 34 (PgDn) &&  пробел 32 && стрелка вправо	39 && позиция scroll в конце страницы
                            // http://www.javascripter.net/faq/keycodes.htm
                            next[0].click();  // жмём на <a>
                        }

                        var prev = $(".pagination-prev");  // находим <a> "обратно"
                        if (prev && (e.which == 8 || e.which == 33  || e.which == 37) && ($(window).scrollTop() == 0 )) {
                             // "обратно" найден && BACK_SPACE 	8 && код клавиши 33 (PgUp) &&  стрелка влево 37 && позиция scroll в конце страницы
                            // http://www.javascripter.net/faq/keycodes.htm
                            prev[0].click();  // жмём на <a>
                        }
                    });
              </script>';
		echo '<div class="pagination"><nav>' . $out . '</nav></div>';
	}

	return $r_orig;
}


function _pagination_pgdn($max, $page_number, $base_url, $diappazon = 4, $url_first = '', $page_u = '', $sep = ' &middot; ', $home_url = '', $sep2 = ' | ')
{
	# (c) http://www.ben-griffiths.com/php-pagination-function/
	# переделал MAX http://maxsite.org/

	if ($max < 2) return '';
	if ($page_number == null) $page_number = 1;
	if ($page_number > $max ) $page_number = $max;
	if ($diappazon < 2) $diappazon = 2;

	$total_pages = $max;
	$total_results_feedback = $max;

	$prev_link_page = $page_number - 1;
	$next_link_page = $page_number + 1;

	if ($prev_link_page < 1) $prev_link_page = 1;

	if ($next_link_page > $total_pages) $next_link_page = $total_pages;

	$middle_page_links = '';

	$pages_start = ($page_number - 3) + 1;

	if ($pages_start < 1) $pages_start = 1;

	$count_to = $pages_start + $diappazon;

	if ($count_to > $total_pages) $count_to = $total_pages;

	$first_mid_link = '';
	$last_mid_link = '';

	for ($counter = $pages_start; $counter <= $count_to; $counter += 1)
	{
		$page_link = $counter;


		if ($counter != $page_number)
		{

			if ($counter == 1)
				$middle_page_links .= '<a href="' . $home_url . '">' . $counter . '</a>';
			else
				$middle_page_links .= '<a href="' . $base_url . $page_u . $page_link . '">' . $counter . '</a>';

			if ($counter < $count_to) $middle_page_links .= $sep;

			if($first_mid_link == '') $first_mid_link = $page_link;

			$last_mid_link = $page_link;
		}
		else
		{
			$middle_page_links .= '<strong>' . $counter . '</strong>';
			if ($counter < $count_to) $middle_page_links .= $sep;
		}
	}

	if ($page_number == 1)
	{
		$first_link = '<span class="pagination-first">%FIRST%</span>' . $sep . '<span class="pagination-prev">%PREV%</span>' . $sep2;
		$first_dots = '';
	}
	else
	{
		if  ($prev_link_page == 1)
			$first_link =  '<a class="pagination-first" href="' . $home_url . '">%FIRST%</a>' . $sep
						. '<a class="pagination-prev" href="' . $home_url . '">%PREV%</a>' . $sep2;
		else
			$first_link =  '<a class="pagination-first" href="' . $home_url . '">%FIRST%</a>' . $sep
						. '<a class="pagination-prev" href="' . $base_url . $page_u . $prev_link_page.'">%PREV%</a>' . $sep2;

		if($page_number > 3)
			$first_dots = ' <a class="pagination-start" href="' . $home_url . '">1</a> ... ';
		else
			$first_dots = '';
	}

	if($page_number == $total_pages)
	{
		$last_link =  $sep2 . '<span class="pagination-next">%NEXT%</span>' . $sep . '<span class="pagination-last">%LAST%</span>';
		$last_dots = '';
	}
	else
	{
		$last_link =  $sep2 . '<a class="pagination-next" href="' . $base_url . $page_u . $next_link_page
					. '">%NEXT%</a>' . $sep . '<a  class="pagination-last" href="' . $base_url . $page_u . $total_pages . '">%LAST%</a>';

		if ( $last_mid_link < $total_pages  )
			$last_dots = ' ... <a class="pagination-end" href="' . $base_url . $page_u . $total_pages . '">' . $total_pages . '</a> ';
		else
			$last_dots = '';
	}

	$output_page_link = $first_link . $first_dots . $middle_page_links. $last_dots . $last_link;

	if ($total_pages == -1)
		$output_page_link = '%FIRST%' . $sep . '%PREV%' . $sep2 . '<strong>1</strong>' . $sep2. '%NEXT%' . $sep . '%LAST%';

	return $output_page_link;
}


function pagination_pgdn_mso_options()
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_pagination_pgdn', 'plugins',
		array(
			'range' => array(
							'type' => 'text',
							'name' => t('Диапазон количества ссылок'),
							'description' => t('Задайте количество отображаемых ссылок на страницы.'),
							'default' => '3'
						),
			'format_first' => array(
							'type' => 'text',
							'name' => t('Текст для «Первая»'),
							'description' => '',
							'default' => '&lt;&lt;'
						),
			'format_prev' => array(
							'type' => 'text',
							'name' => t('Текст для «предыдущая»'),
							'description' => '',
							'default' => '&lt;'
						),
			'format_next' => array(
							'type' => 'text',
							'name' => t('Текст для «следующая»'),
							'description' => '',
							'default' => '&gt;'
						),
			'format_last' => array(
							'type' => 'text',
							'name' => t('Текст для «последняя»'),
							'description' => '',
							'default' => '&gt;&gt;'
						),
			'sep' => array(
							'type' => 'text',
							'name' => t('Разделитель между страницами'),
							'description' => '',
							'default' => ' '
						),
			'sep2' => array(
							'type' => 'text',
							'name' => t('Разделитель между блоком страниц и текстовыми ссылками'),
							'description' => '',
							'default' => ' '
						),

			),
		t('Настройки плагина pagination_pgdn'), // титул
		t('Укажите необходимые опции.' )  // инфо
	);
}


# end file