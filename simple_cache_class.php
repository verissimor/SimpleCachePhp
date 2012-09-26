<?php

class SimpleCachePhp {
    /* private $folderCache = sistemaCache::obterDocumentRoot() . "cache";
      $filename = "aluguel-lang$ididioma.html";
      $fileFullName = $folderCache . "/" . $filename; */

    private $folderCache;
    private $filename;

    function __construct($filename, $time = 86400, $ignoreKeysOnParametrize = array(), $folderCache = "") {
        if (!is_array($ignoreKeysOnParametrize))
            die("SimpleCachePhp: A variável ignoreKeysOnParametrize deve ser um array!");

        $filename = self::getFileName($filename, $ignoreKeysOnParametrize);

        if (!$folderCache)
            $folderCache = self::obterDocumentRoot() . "cache";

        $fileFullName = $folderCache . "/" . $filename;
        if (self::verificaCacheFile($fileFullName, $time)) {
            include_once $fileFullName;
            echo "<!--em cache: $filename-->";
            exit();
        } else {
            $this->folderCache = $folderCache;
            $this->filename = $filename;
            ob_start();
        }
    }

    public function CacheEnd() {
        $s = ob_get_contents();
        self::criarFileCache($this->folderCache, $this->filename, $s);
        ob_end_flush();
    }

    private function getFileName($filename, $ignoreKeysOnParametrize) {
        if (!isset($_SESSION)) {
            session_start();
        }

        $s = basename($filename);
        $s .= "_";
        $s .= "_-s-_" . self::parameterize_array($_SESSION, $ignoreKeysOnParametrize);
        $s .= "_-q-_" . self::parameterize_array($_GET, $ignoreKeysOnParametrize);
        return self::toRewriteString($s);
    }

    private static function parameterize_array($array, $ignoreKeysOnParametrize) {
        $out = array();
        foreach ($array as $key => $value) {
            if (!in_array($key, $ignoreKeysOnParametrize))
                $out[] = "$key-$value";
        }

        return join("_", $out);
    }

    private static function toRewriteString($s) {
        $s = trim($s);
        $s = mb_strtolower($s, 'UTF-8');

        //Letra a
        $s = str_replace("á", "a", $s);
        $s = str_replace("à", "a", $s);
        $s = str_replace("ã", "a", $s);
        $s = str_replace("â", "a", $s);
        $s = str_replace("ä", "a", $s);

        //letra e
        $s = str_replace("é", "e", $s);
        $s = str_replace("ê", "e", $s);
        $s = str_replace("è", "e", $s);
        $s = str_replace("ë", "e", $s);

        //letra i
        $s = str_replace("í", "i", $s);
        $s = str_replace("ì", "i", $s);
        $s = str_replace("î", "i", $s);
        $s = str_replace("ï", "i", $s);

        //letra o
        $s = str_replace("ó", "o", $s);
        $s = str_replace("ô", "o", $s);
        $s = str_replace("õ", "o", $s);
        $s = str_replace("ò", "o", $s);
        $s = str_replace("ö", "o", $s);

        //letra u
        $s = str_replace("ú", "u", $s);
        $s = str_replace("ü", "u", $s);
        $s = str_replace("û", "u", $s);
        $s = str_replace("ù", "u", $s);

        //letra c
        $s = str_replace("ç", "c", $s);

        //ultimos caracteres indesejaveis
        $s = str_replace("  ", " ", $s);
        $s = str_replace(" ", "-", $s);

        $s = preg_replace("/[^a-zA-Z0-9_.-]/", "", $s);
        $s = str_replace("-.", ".", $s);
        return $s;
    }

    /**
     * Verficia se o arquivo de cache existe e se o seu tempo é maior que a variavel time. Unidade de medida time é em segundos ex.: 5 * 60 = 5 min
     * @param string $filename Nome do arquivo
     * @param int $time Tempo que o arquivo deve estar em cache
     * @return boolean 
     */
    public static function verificaCacheFile($filename, $time = 86400) {
        if (file_exists($filename) && (time() - $time < filemtime($filename))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 
     * Verifica se os diretorio de cache existe e se possui permissoes de escrita
     * @param string $folder Pasta que os arquivos de cache serao armazenados.
     * @return boolean 
     */
    private static function verificaDiretorios($folder) {

        if (!is_dir($folder)) {
            if (!mkdir($folder)) {
                die("sistemaCache: Nao foi possivel criar o diretorio $folder. Tente criar via ftp, liberando as permissoes de escrita.");
                return false;
            }
        }


        if (!is_writable($folder)) {
            if (!chmod($folder, 0777)) {
                die("sistemaCache: O diretorio $folder nao possui permissao de escrita. Tente criar via ftp, liberando as permissoes de escrita.");
                return false;
            }
        }

        return true;
    }

    public static function criarFileCache($folderCache, $fileName, $conteudo) {
        self::verificaDiretorios($folderCache);

        $filename = $folderCache . "/" . $fileName;

        $fp = @fopen($filename, "w");
        if ($fp) {
            fwrite($fp, $conteudo);
            fclose($fp);

            if (file_exists($filename)) {
                chmod($filename, 0777);
                return true;
            }
        }

        echo "<!-- SimpleCachePhp: Nao foi possível criar o cache. -->";
        return false;
    }

    public static function obterDocumentRoot() {
        $AppRoot = $_SERVER['DOCUMENT_ROOT'];

        if ($AppRoot[strlen($AppRoot) - 1] != "/") {
            $AppRoot.= "/";
        }

        return $AppRoot;
    }

}

