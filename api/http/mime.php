<?php
/**
 * waggo8
 * @copyright 2013-2021 CIEL, K.K., project waggo.
 * @license MIT
 */

global $WG_MIMETYPES;

$WG_MIMETYPES=["tsp"=>"application/dsptype",
			"lcc"=>"application/fastman",
			"pfr"=>"application/font-tdpfr",
			"spl"=>"application/futuresplash",
			"hqx"=>"application/mac-binhex40",
			"cpt"=>"application/mac-compactpro",
			"pot"=>"application/mspowerpoint",
			"pps"=>"application/mspowerpoint",
			"ppt"=>"application/mspowerpoint",
			"ppz"=>"application/mspowerpoint",
			"doc"=>"application/msword",
			"bin"=>"application/octet-stream",
			"class"=>"application/octet-stream",
			"dms"=>"application/octet-stream",
			"exe"=>"application/octet-stream",
			"jar"=>"application/octet-stream",
			"sea"=>"application/octet-stream",
			"oda"=>"application/oda",
			"pdf"=>"application/pdf",
			"ai"=>"application/postscript",
			"eps"=>"application/postscript",
			"ps"=>"application/postscript",
			"rtf"=>"application/rtf",
			"smi"=>"application/smil",
			"svi"=>"application/softvision",
			"ttz"=>"application/t-time",
			"aab"=>"application/x-authorware-bin",
			"aam"=>"application/x-authorware-map",
			"aas"=>"application/x-authorware-seg",
			"bcpio"=>"application/x-bcpio",
			"bz2"=>"application/x-bzip2",
			"cqk"=>"application/x-calquick",
			"vcd"=>"application/x-cdlink",
			"ccn"=>"application/x-cnc",
			"cco"=>"application/x-cocoa",
			"Z"=>"application/x-compress",
			"cpio"=>"application/x-cpio",
			"csh"=>"application/x-csh",
			"dcr"=>"application/x-director",
			"dir"=>"application/x-director",
			"dxr"=>"application/x-director",
			"dvi"=>"application/x-dvi",
			"xls"=>"application/x-excel",
			"ebk"=>"application/x-expandedbook",
			"gtar"=>"application/x-gtar",
			"gz"=>"application/x-gzip",
			"hdf"=>"application/x-hdf",
			"cgi"=>"application/x-httpd-cgi",
			"js"=>"application/x-javascript",
			"ls"=>"application/x-javascript",
			"mocha"=>"application/x-javascript",
			"skd"=>"application/x-koan",
			"skm"=>"application/x-koan",
			"skp"=>"application/x-koan",
			"skt"=>"application/x-koan",
			"latex"=>"application/x-latex",
			"lha"=>"application/x-lzh",
			"lzh"=>"application/x-lzh",
			"mps"=>"application/x-mapserver",
			"mct"=>"application/x-mascot",
			"mif"=>"application/x-mif",
			"cdf"=>"application/x-netcdf",
			"nc"=>"application/x-netcdf",
			"pac"=>"application/x-ns-proxy-auto-config",
			"mpp"=>"application/x-pixelscooter",
			"sh"=>"application/x-sh",
			"shar"=>"application/x-shar",
			"swf"=>"application/x-shockwave-flash",
			"spr"=>"application/x-sprite",
			"sprite"=>"application/x-sprite",
			"spt"=>"application/x-spt",
			"sit"=>"application/x-stuffit",
			"sv4cpio"=>"application/x-sv4cpio",
			"sv4crc"=>"application/x-sv4crc",
			"tar"=>"application/x-tar",
			"tgz"=>"application/x-tar",
			"tcl"=>"application/x-tcl",
			"tex"=>"application/x-tex",
			"texi"=>"application/x-texinfo",
			"texinfo"=>"application/x-texinfo",
			"roff"=>"application/x-troff",
			"t"=>"application/x-troff",
			"tr"=>"application/x-troff",
			"man"=>"application/x-troff-man",
			"me"=>"application/x-troff-me",
			"ms"=>"application/x-troff-ms",
			"ustar"=>"application/x-ustar",
			"src"=>"application/x-wais-source",
			"xdm"=>"application/x-xdma",
			"xdma"=>"application/x-xdma",
			"zip"=>"application/zip",
			"odb"=>"application/vnd.oasis.opendocument.database",
			"ods"=>"application/vnd.oasis.opendocument.spreadsheet",
			"odt"=>"application/vnd.oasis.opendocument.text",
			"au"=>"audio/basic",
			"snd"=>"audio/basic",
			"es"=>"audio/echospeech",
			"kar"=>"audio/midi",
			"mid"=>"audio/midi",
			"midi"=>"audio/midi",
			"mp2"=>"audio/mpeg",
			"mp3"=>"audio/mpeg",
			"mpga"=>"audio/mpeg",
			"tsi"=>"audio/tsplayer",
			"ra"=>"audio/vnd.rn-realaudio",
			"vox"=>"audio/voxware",
			"aif"=>"audio/x-aiff",
			"aifc"=>"audio/x-aiff",
			"aiff"=>"audio/x-aiff",
			"aba"=>"audio/x-bamba",
			"cha"=>"audio/x-chacha",
			"mio"=>"audio/x-mio",
			"ram"=>"audio/x-pn-realaudio",
			"rm"=>"audio/x-pn-realaudio",
			"rpm"=>"audio/x-pn-realaudio-plugin",
			"vqf"=>"audio/x-twinvq",
			"vql"=>"audio/x-twinvq",
			"vqe"=>"audio/x-twinvq-plugin",
			"wav"=>"audio/x-wav",
			"csm"=>"chemical/x-csml",
			"emb"=>"chemical/x-embl-dl-nucleotide",
			"gau"=>"chemical/x-gaussian-input",
			"mol"=>"chemical/x-mdl-molfile",
			"mop"=>"chemical/x-mopac-input",
			"pdb"=>"chemical/x-pdb",
			"xyz"=>"chemical/x-xyz",
			"ivr"=>"i-world/i-vrml",
			"bmp"=>"image/bmp",
			"fif"=>"image/fif",
			"gif"=>"image/gif",
			"ief"=>"image/ief",
			"jpg"=>"image/jpeg",
			"jpe"=>"image/jpeg",
			"jpeg"=>"image/jpeg",
			"png"=>"image/png",
			"tif"=>"image/tiff",
			"tiff"=>"image/tiff",
			"mcf"=>"image/vasa",
			"rp"=>"image/vnd.rn-realpix",
			"ras"=>"image/x-cmu-raster",
			"fh"=>"image/x-freehand",
			"fh4"=>"image/x-freehand",
			"fh5"=>"image/x-freehand",
			"fh7"=>"image/x-freehand",
			"fhc"=>"image/x-freehand",
			"jps"=>"image/x-jps",
			"pnm"=>"image/x-portable-anymap",
			"pbm"=>"image/x-portable-bitmap",
			"pgm"=>"image/x-portable-graymap",
			"ppm"=>"image/x-portable-pixmap",
			"rgb"=>"image/x-rgb",
			"xbm"=>"image/x-xbitmap",
			"xpm"=>"image/x-xpixmap",
			"swx"=>"image/x-xres",
			"xwd"=>"image/x-xwindowdump",
			"ptlk"=>"plugin/listenup",
			"waf"=>"plugin/wanimate",
			"wan"=>"plugin/wanimate",
			"css"=>"text/css",
			"htm"=>"text/html",
			"html"=>"text/html",
			"txt"=>"text/plain",
			"rtx"=>"text/richtext",
			"tsv"=>"text/tab-separated-values",
			"rt"=>"text/vnd.rn-realtext",
			"etx"=>"text/x-setext",
			"sgm"=>"text/x-sgml",
			"sgml"=>"text/x-sgml",
			"talk"=>"text/x-speech",
			"vcf"=>"text/x-vcard",
			"xml"=>"text/xml",
			"xsl"=>"text/xsl",
			"mpe"=>"video/mpeg",
			"mpeg"=>"video/mpeg",
			"mpg"=>"video/mpeg",
			"mp4"=>"video/mp4",
			"mov"=>"video/quicktime",
			"qt"=>"video/quicktime",
			"rv"=>"video/vnd.rn-realvideo",
			"viv"=>"video/vnd.vivo",
			"vivo"=>"video/vnd.vivo",
			"vba"=>"video/x-bamba",
			"asf"=>"video/x-ms-asf",
			"asx"=>"video/x-ms-asf",
			"avi"=>"video/x-msvideo",
			"qm"=>"video/x-qmsys",
			"movie"=>"video/x-sgi-movie",
			"tgo"=>"video/x-tango",
			"vif"=>"video/x-vif",
			"wmv"=>"video/x-ms-wmv",
			"wmx"=>"video/x-ms-wmx",
			"wvx"=>"video/x-ms-wvx",
			"3gp"=>"video/3gpp",
			"3g2"=>"video/3gpp2",
			"flv"=>"video/x-flv",
			"vts"=>"workbook/formulaone",
			"pan"=>"world/x-panoramix",
			"ice"=>"x-conference/x-cooltalk",
			"d96"=>"x-world/x-d96",
			"mus"=>"x-world/x-d96",
			"svr"=>"x-world/x-svr",
			"vrml"=>"x-world/x-vrml",
			"wrl"=>"x-world/x-vrml",
			"vrt"=>"x-world/x-vrt"
];

/**
 * @param $mimetype
 * @return bool
 * @see https://www.iana.org/assignments/media-types/media-types.xhtml
 */

function wg_mimetype_is_image($mimetype)
{
	@list($p) = explode('/', $mimetype);
	return !empty($p) && strtolower($p) === 'image';
}

function wg_mimetype_is_movie($mimetype)
{
	@list($p) = explode('/', $mimetype);
	return !empty($p) && strtolower($p) === 'video';
}

function wg_mimetype_is_audio($mimetype)
{
	@list($p) = explode('/', $mimetype);
	return !empty($p) && strtolower($p) === 'audio';
}

function wg_mimetype_is_pdf($mimetype)
{
	$p = [
		"application/pdf"
	];
	return in_array($mimetype,$p);
}

function wg_mimetype_is_document($mimetype)
{
	$p = [
		"application/pdf",
		"application/mspowerpoint",
		"application/msword",
		"application/x-excel",
		"application/vnd.oasis.opendocument.database",
		"application/vnd.oasis.opendocument.spreadsheet",
		"application/vnd.oasis.opendocument.text"
	];
	return in_array($mimetype,$p);
}

function wg_mimetype_from_ext($ext)
{
	global $WG_MIMETYPES;
	$e = strtolower($ext);
	if(in_array($e,array_keys($WG_MIMETYPES))) return $WG_MIMETYPES[$e];
	return "application/octet-stream";
}
