<?php if ($namespace):?>
namespace App\Http\Controllers\<?=$namespace?>;
    <?php else: ?>
namespace App\Http\Controllers;
<?php endif; ?>

<?php if($type == 'api'):?>
use App\Http\Controllers\Controller;
<?php elseif ($type == 'admin'):?>
use App\Http\Controllers\Admin\AdminBaseController;
<?php endif;?>
use Illuminate\Http\Request;

/**
 * Created by PhpStorm.
 * User: healer
 * Date: <?=date('Y-m-d');?>

 * Time: <?=date('H:i:s');?>

 */
class <?=$controllerName?> extends <?php if($type == 'api'):?>Controller<?php elseif ($type == 'admin'):?>AdminBaseController<?php endif;?>

{

    /*__ACTION_PLACEHOLDER__*/
}

