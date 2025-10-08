<?php
class zm_form{

	public $options;
	public $zm_sh;
	public $iconsets;

	function __construct($options = ""){
		global $zm_sh, $zm_sh_default_options;
		$this->zm_sh	= &$zm_sh;
		$this->iconsets	= &$zm_sh->iconsets;
		$this->options = $options ? $options : get_option("zm_shbt_fld", $zm_sh_default_options);
	}

	function text($id, $label, $name = false, $value = false){
		$name	= $name		? $name		: "zm_shbt_fld[$id]";
		$value	= $value	? $value	: $this->options[$id];
		echo "<div class='row'>";
			echo "<label for='".esc_attr($id)."' style='width:140px;line-height: 37px;'>".esc_html($label)."</label>";
			echo "<input type='text' id='".esc_attr($id)."' name='".esc_attr($name)."' value='".esc_attr($value)."' style='width: 278px;height: 33px;	background-color: #ffffff;border: 1.2px solid #B8B8B8;' >";
		echo "</div>";
	}

	function textArea($id, $label, $name = false, $value = false){
		$name	= $name		? $name		: "zm_shbt_fld[$id]";
		$value	= $value	? $value	: (isset($this->options[$id]) ? $this->options[$id] : '');
		echo "<div class='row'>";
			echo "<label for='".esc_attr($id)."' style='width:140px;line-height: 37px;'>".esc_html($label)."</label>";
			echo "<textarea type='text' id='".esc_attr($id)."' name='".esc_attr($name)."' style='width: 278px;background-color: #ffffff;border: 1.2px solid #B8B8B8;' placeholder='Exclude by Page ID, Page Title or Page Slug' >".esc_textarea($value)."</textarea>";
		echo "</div>";
	}


	function checkbox($id, $label, $name = '', $selected=null, $class = '', $yes = "", $no = "", $id_prefix="",$description=''){
		$yes = $yes?$yes:__("Yes", "zm_sh");
		$no = $no?$no:__("No", "zm_sh");
		$class	= $class?$class:'toggle-check';
		$saved_val = isset($this->options[$id])?$this->options[$id]:false;
		$chk = $selected===null?checked($saved_val, true, false):$selected;
		$name = $name ? $name : "zm_shbt_fld[$id]";
		echo "<div class='row'>";
			echo "<label for='".esc_attr($id_prefix.$id)."' title='".esc_attr($description)."'>".esc_html($label)."</label>";
			echo "<input name='".esc_attr($name)."' id='".esc_attr($id_prefix.$id)."' $chk type='checkbox' value='1' data-id='".esc_attr($id)."' />";
			echo "<span class='for_label'>";
				echo "<label for='".esc_attr($id_prefix.$id)."' class='".esc_attr($class)."' data-on='".esc_attr($yes)."' data-off='".esc_attr($no)."'></label>";
			echo "</span>";
		echo "</div>";
		if($description)
			echo "<p>".esc_html($description)."</p>";
	}

	function show_on($id, $label, $selected=false, $class = 'toggle-check', $yes = "", $no = ""){
		$options = $this->options;
		$iconset = $this->iconsets->get_iconset($options['iconset']);
		$yes	= $yes?$yes:__("Yes", "zm_sh");
		$no		= $no?$no:__("No", "zm_sh");
		$name	= "zm_shbt_fld[show_in][$id]";
		$name_1	= "zm_shbt_fld[$id]";
		if(isset($options['show_in'][$id]) and $options['show_in'][$id] )
			$chk	= "checked='checked'";
		else
			$chk	= '';
		if(!isset($options[$id]) or !$options[$id]){
			$tmp_array = array_slice($iconset->types, 0, 1);
			$options[$id] = array_shift($tmp_array);
		}
		//if($chk and !$circle and !$square)
			//$circle = 'checked="checked"';
		echo "<div class='row toggle'>";
			echo "<label for='$id'>$label</label>";
			echo "<input id='$id' $chk type='checkbox' name='{$name}' value='1'/>";
			echo "<span class='for_label'>";
				echo "<label for='$id' class='$class' data-on='$yes' data-off='$no'></label>";

				echo "<div class='row show_on' style='margin-top:50px'>";
					foreach($iconset->types as $type){
						if(isset($options[$id]) and $options[$id])
							$selected = checked($options[$id], $type, false);
						else
							$selected = '';
						echo "<input type='radio' id='$id-$type' name='$name_1' value='$type' $selected >";
						echo "<label for='$id-$type'><img src='". zm_sh_url_assets_img . $id . "-$type.png'></label>";
					}
				echo "</div>";
			echo "</span>";
			//print_r($options);
		echo "</div>";
	}

	function icon_fields($label, $label_prefix, $class = 'toggle-check', $yes = "", $no = ""){
		$icons = $this->iconsets->get_iconset($this->options['iconset'])->get_icons();
		echo "<div class='row' style='margin-bottom:20px'>";
			echo "<h2>$label</h2>";
		echo "</div>";
		foreach($icons as $icon){
			$id		= $icon['id'];
			$name	= $icon['name'];
			$c		= isset($this->options['icons'][$id])?$this->options['icons'][$id]:false;
			$chk	= checked($c, true, false);
			$this->checkbox($id, $label_prefix.' '.$name, "zm_shbt_fld[icons][$id]", $chk, $class, $yes, $no, 'icon_');
		}
	}

	function icon_fields_widget($id, $name, $selected_icons, $label, $label_prefix, $iconset){
		$icons = $this->iconsets->get_iconset($iconset)->get_icons();
		//print_r(func_get_args());
		echo "<div class='row' style='margin-bottom:20px'>";
			echo "<h2>".esc_html($label)."</h2>";
		echo "</div>";
		$selected_icons = $selected_icons?$selected_icons:array();
		foreach($icons as $icon){
			$_id	= $icon['id'];
			$_name	= $icon['name'];
			//echo $_id. '||' .$selected_icons[$_id]."\n";
			if(isset($selected_icons[$_id]))
				$chk	= checked($selected_icons[$_id], true, false);
			else
				$chk	= false;
			$this->checkbox($id.'_'.$_id, $label_prefix.' '.$_name, $name . "[$_id]", $chk);
		}
	}

	function dropdown($id, $label, $items, $name=false, $selected=false){
		echo "<div class='row'>";
			echo "<label for='".esc_attr($id)."'>".esc_html($label)."</label>";
			echo "<select id='".esc_attr($id)."' name='".esc_attr($name)."'>";
			foreach($items as $item){
				$selec = selected($selected, $item, false);
				echo "<option value='".esc_attr($item)."' $selec>".esc_html(ucwords($item))."</option>";
			}
			echo "</select>";
		echo "</div>";
	}

	function _select_iconset($id, $label, $items, $name=false, $selected='default'){
		$name = $name ? $name : "zm_shbt_fld[$id]";
		$selected = $selected ? $selected : $this->options[$id];
		//$iconset = $this->iconsets->get_iconset($selected);
		echo "<div class='row'>";
			echo "<label for='".esc_attr($id)."'>".esc_html($label)."</label>";
			echo "<select id='".esc_attr($id)."' name='".esc_attr($name)."'>";
			foreach($items as $i_id=>$i_name){
				$selec = selected($selected, $i_id, false);
				echo "<option value='".esc_attr($i_id)."' $selec>".esc_html($i_name)."</option>";
			}
			echo "</select>";
			//print_r($this->zm_sh->iconsets->get_iconset($selected));
			?>
			<div class="button-style-img">
				<img src="<?php echo esc_url($this->iconsets->get_iconset($selected)->get_iconset_preview()); ?>" alt="" class="" />
			</div>
			<?php
		echo "</div>";
	}

	function select_iconset($id, $label, $name=false, $selected=false ){
		$iconsets = $this->iconsets->get_iconset_list();
		$this->_select_iconset($id, $label, $iconsets, $name, $selected );
	}



}