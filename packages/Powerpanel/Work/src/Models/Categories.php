<?php
/**
 * This helper generates dynamic categories
 * @package Netquick
 * @version 1.00
 * @since 2017-02-09
 * @author Vishal Agrawal
 */
namespace Powerpanel\Services\Models;

use App\Helpers\MyLibrary;

class Categories
{
    public static function Parentcategoryhierarchy($selected_id = false, $post_id = false, $modelNameSpace = false)
    {

        $style    = "style='display: none'";
        $dipnopar = "selected";

        if ($modelNameSpace == false || $modelNameSpace == '') {
            $modelNameSpace = MyLibrary::getModelNameSpace();
        }
        $query = $modelNameSpace::getCategories();

        $query = $query->get();

        $children = array();
        $pitems   = array();
        foreach ($query as $row) {
            $pitems[] = $row;
        }
        if ($pitems) {
            foreach ($pitems as $p) {
                $pt   = $p->intParentCategoryId;
                $list = @$children[$pt] ? $children[$pt] : array();
                array_push($list, $p);
                $children[$pt] = $list;
            }
        }
        $list = Self::treerecurse(0, '', array(), $children, 5, 0, 0);

        $output = '<select multiple class="form-control select2" data-show-subtext="true" size="10" name="category_id[]" id="category_id" placeholder="Parent category" >';
        //$output .="<option value=\"0\" " . (($selected_id == 0) ? $dipnopar : '') . ">No Parent Category</option>";
        $temp1 = "";
        $temp  = "";
        //$disabled = "";
        $tempfk = "";
        foreach ($list as $item) {

            if ($item->intParentCategoryId == 0) {
                $parentClass = 'normalcategory';
            } else {
                $parentClass = 'ParentCategoryName';
            }

            if (!empty($selected_id)) {
                $output .= "<option class=" . $parentClass . " value=" . $item->id . " " . ((in_array($item->id, $selected_id)) ? 'selected' : '') . ">" . htmlspecialchars(html_entity_decode($item->treename)) . "</option>";
            } else {
                $output .= "<option class=" . $parentClass . " value=" . $item->id . ">" . htmlspecialchars(html_entity_decode($item->treename)) . "</option>";
            }

        }
        $output .=  "<option value='addCat'>Add Category</option>";
        $output .= "</select>";
        return $output;

    }

    public static function treerecurse($id, $indent, $list = array(), $children = array(), $maxlevel = '5', $level = 0, $type = 1)
    {
        if (isset($children[$id])) {

            if ($children[$id] && $level <= $maxlevel) {
                foreach ($children[$id] as $c) {
                    $id = $c->id;
                    if ($type) {
                        $pre    = '<sup>|_</sup>&nbsp;';
                        $spacer = '.&nbsp;&nbsp;&nbsp;';
                    } else {
                        $pre    = '|_ ';
                        $spacer = '&nbsp;&nbsp;&nbsp;';
                    }

                    if ($c->intParentCategoryId == 0) {
                        $txt = $c->varTitle;
                    } else {
                        $txt = $pre . $c->varTitle;
                    }

                    $pt                       = $c->intParentCategoryId;
                    $list[$id]                = $c;
                    $list[$id]->treename      = "$indent$txt";
                    $list[$id]->endDepthlevel = "No";
                    if ($level == $maxlevel) {
                        $list[$id]->endDepthlevel = "Yes";
                    }
                    if (isset($list[$id]) && isset($list[$id]->children)) {
                        $list[$id]->children = count($children[$id]);
                    }
                    $list = Self::treerecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level + 1, $type);
                }
            }

        }
        return $list;
    }
}
