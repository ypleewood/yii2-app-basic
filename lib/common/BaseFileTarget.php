<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\lib\common;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;
use yii\log\FileTarget;

/**
 * FileTarget records log messages in a file.
 *
 * The log file is specified via [[logFile]]. If the size of the log file exceeds
 * [[maxFileSize]] (in kilo-bytes), a rotation will be performed, which renames
 * the current log file by suffixing the file name with '.1'. All existing log
 * files are moved backwards by one place, i.e., '.2' to '.3', '.1' to '.2', and so on.
 * The property [[maxLogFiles]] specifies how many history files to keep.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BaseFileTarget extends FileTarget
{

	//是否按日志拆分日志
	public $isDaily = true;


	/**
	 * 按日期拆分日志，仅保留一定时长的日志
	 */
	public function dailyExport() {
		$nameArr = explode('.', $this->logFile);
		if(count($nameArr) < 2) {
			$nameArr[1] = 'log';
		}
		$max = $this->maxLogFiles;
		$oldDate = date("Y-m-d",strtotime("-".$max." day"));
		$oldLogFile = $nameArr[0].'_'.$oldDate. '.'.$nameArr[1];

		for($ind = $max;file_exists($oldLogFile); $ind++) {
			@unlink($oldLogFile);
			
			for($b = 1; $b <= $this->maxLogFiles; $b ++) {
				$backupFile = $oldLogFile.'.'.$b;

				if(file_exists($backupFile)) {
					@unlink($backupFile);
				} else {
					break;
				}
			}

			$ind++;
			$oldDate = date("Y-m-d",strtotime("-".$ind." day"));
			$oldLogFile = $nameArr[0].'_'.$oldDate. '.'.$nameArr[1];

		}
	
		$this->logFile = $nameArr[0].'_'.date('Y-m-d').'.'.$nameArr[1];
	}

    /**
     * Writes log messages to a file.
     * Starting from version 2.0.14, this method throws LogRuntimeException in case the log can not be exported.
     * @throws InvalidConfigException if unable to open the log file for writing
     * @throws LogRuntimeException if unable to write complete log to file
     */
    public function export()
    {
		if($this->isDaily) {
			$this->dailyExport();
		}
        if (strpos($this->logFile, '://') === false || strncmp($this->logFile, 'file://', 7) === 0) {
            $logPath = dirname($this->logFile);
            FileHelper::createDirectory($logPath, $this->dirMode, true);
        }

        $text = implode("\n", array_map([$this, 'formatMessage'], $this->messages)) . "\n";
        if (($fp = @fopen($this->logFile, 'a')) === false) {
            throw new InvalidConfigException("Unable to append to log file: {$this->logFile}");
        }
        @flock($fp, LOCK_EX);
        if ($this->enableRotation) {
            // clear stat cache to ensure getting the real current file size and not a cached one
            // this may result in rotating twice when cached file size is used on subsequent calls
            clearstatcache();
        }
        if ($this->enableRotation && @filesize($this->logFile) > $this->maxFileSize * 1024) {
            @flock($fp, LOCK_UN);
            @fclose($fp);
            $this->rotateFiles();
            $writeResult = @file_put_contents($this->logFile, $text, FILE_APPEND | LOCK_EX);
            if ($writeResult === false) {
                $error = error_get_last();
                throw new LogRuntimeException("Unable to export log through file!: {$error['message']}");
            }
            $textSize = strlen($text);
            if ($writeResult < $textSize) {
                throw new LogRuntimeException("Unable to export whole log through file! Wrote $writeResult out of $textSize bytes.");
            }
        } else {
            $writeResult = @fwrite($fp, $text);
            if ($writeResult === false) {
                $error = error_get_last();
                throw new LogRuntimeException("Unable to export log through file!: {$error['message']}");
            }
            $textSize = strlen($text);
            if ($writeResult < $textSize) {
                throw new LogRuntimeException("Unable to export whole log through file! Wrote $writeResult out of $textSize bytes.");
            }
            @flock($fp, LOCK_UN);
            @fclose($fp);
        }
        if ($this->fileMode !== null) {
            @chmod($this->logFile, $this->fileMode);
        }
    }

}
