<?php
		function humanReadableDuration($sec) {
			$years = (int)($sec/31556926);
			$secAfterYears = $sec - ($years*31556926);
			$months = (int)($secAfterYears/2629743.83);
			$secAfterMonth = $secAfterYears - ($months*2629743.83);
			$days = (int)($secAfterMonth/86400);
			$secAfterDays = $secAfterMonth - ($days*86400);
			$hours = (int)($secAfterDays/3600);
			$secAfterHours = $secAfterDays - ($hours*3600);
			$min = (int) date("i", $secAfterHours);
			$secAfterMinutes = $secAfterHours - ($min*60);
			$sec = date("s", $secAfterMinutes);

      $prefix = "";
      $str = "";
      if ($years>0) $str .= "$years year"; if ($years>1) $str.="s"; if ($years>0) $str.=" ";
      if ($months>0) $str .= "$months month"; if ($months>1) $str.="s"; if ($months>0) $str.=" ";
      
      if ($years>0 and $secAfterMonth>0) {
        $prefix = "more than ";
      } else {
        if ($days>0) $str .= "$days day"; if ($days>1) $str.="s"; if ($days>0) $str.=" ";
        if ($months>0 and $secAfterDays>0) {
          $prefix = "more than ";
        } else {
          if ($hours>0) $str .= "$hours hour"; if ($hours>1) $str.="s"; if ($hours>0) $str.=" ";
          if ($days>0 and $secAfterHours>0) {
              $prefix = "more than ";
            } else {
            if ($min>0) $str .= "$min minute"; if ($min>1) $str.="s"; if ($min>0) $str.=" ";
            if ($sec>0) $str .= "$sec second"; if ($sec>1) $str.="s"; if ($sec>0) $str.=" ";
          }
        }
      }
			return $prefix.$str;
		}
?>
