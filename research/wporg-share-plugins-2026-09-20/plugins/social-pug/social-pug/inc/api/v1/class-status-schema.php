<?php

namespace Mediavine\Grow\API\V1;

/**
 * Status resource schema.
 *
 * @internal
 */
class Status_Schema {

	/**
	 * Get the schema config, formatted for JSON Schema.
	 *
	 * @return array
	 */
	public function json_schema() : array {
		$schema = [
			'$schema'    => 'https://json-schema.org/draft/2020-12/schema',
			'title'      => 'grow-social-status',
			'type'       => 'object',
			'properties' => [
				'is_pro'  => [
					'description' => esc_html__( 'Are pro features available?', 'mediavine' ),
					'type'        => 'boolean',
				],
				'version' => [
					'description' => esc_html__( 'Current Hubbub version', 'mediavine' ),
					'pattern'     => '^[\\d]+\\.[\\d]+\\.[\\d]$',
					'type'        => [ 'null', 'string' ],
				],
			],
			'required'   => [ 'is_pro', 'version' ],
		];

		return $schema;
	}
}
