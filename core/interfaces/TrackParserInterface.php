<?php
namespace RunningRoutes\Core;

interface TrackParserInterface {
	public function parse( string $file_path ): array; // возвращает точки, метаданные и т.д.
}