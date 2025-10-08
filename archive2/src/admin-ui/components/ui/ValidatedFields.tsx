import React from 'react';
import { FormField, TextInput } from '.';

interface ValidatedTextInputProps {
	label: string;
	value: string;
	onChange: ( value: string ) => void;
	onBlur?: () => void;
	error?: string | null;
	required?: boolean;
	placeholder?: string;
	type?: 'text' | 'email' | 'url' | 'password';
	disabled?: boolean;
	helpText?: string;
	className?: string;
}

export const ValidatedTextInput: React.FC< ValidatedTextInputProps > = ( {
	label,
	value,
	onChange,
	onBlur,
	error,
	required,
	placeholder,
	type = 'text',
	disabled,
	helpText,
	className,
} ) => {
	return (
		<FormField
			label={ label }
			required={ required }
			className={ className }
		>
			<div className="space-y-1">
				<TextInput
					type={ type }
					value={ value }
					onChange={ onChange }
					onBlur={ onBlur }
					placeholder={ placeholder }
					disabled={ disabled }
					className={
						error
							? 'border-red-500 focus:border-red-500 focus:ring-red-500'
							: ''
					}
				/>

				{ error && (
					<p className="text-sm text-red-600 flex items-center">
						<i className="fas fa-exclamation-triangle mr-1" />
						{ error }
					</p>
				) }

				{ helpText && ! error && (
					<p className="text-sm text-gray-500">{ helpText }</p>
				) }
			</div>
		</FormField>
	);
};

interface ValidatedSelectProps {
	label: string;
	value: string;
	onChange: ( value: string ) => void;
	onBlur?: () => void;
	error?: string | null;
	required?: boolean;
	disabled?: boolean;
	options: Array< { value: string; label: string } >;
	placeholder?: string;
	helpText?: string;
	className?: string;
}

export const ValidatedSelect: React.FC< ValidatedSelectProps > = ( {
	label,
	value,
	onChange,
	onBlur,
	error,
	required,
	disabled,
	options,
	placeholder,
	helpText,
	className,
} ) => {
	return (
		<FormField
			label={ label }
			required={ required }
			className={ className }
		>
			<div className="space-y-1">
				<select
					value={ value }
					onChange={ ( e ) => onChange( e.target.value ) }
					onBlur={ onBlur }
					disabled={ disabled }
					className={ `
            w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500
            ${
				error
					? 'border-red-500 focus:border-red-500 focus:ring-red-500'
					: 'border-gray-300'
			}
            ${ disabled ? 'bg-gray-100 cursor-not-allowed' : 'bg-white' }
          ` }
				>
					{ placeholder && (
						<option value="" disabled>
							{ placeholder }
						</option>
					) }
					{ options.map( ( option ) => (
						<option key={ option.value } value={ option.value }>
							{ option.label }
						</option>
					) ) }
				</select>

				{ error && (
					<p className="text-sm text-red-600 flex items-center">
						<i className="fas fa-exclamation-triangle mr-1" />
						{ error }
					</p>
				) }

				{ helpText && ! error && (
					<p className="text-sm text-gray-500">{ helpText }</p>
				) }
			</div>
		</FormField>
	);
};

interface ValidatedTextAreaProps {
	label: string;
	value: string;
	onChange: ( value: string ) => void;
	onBlur?: () => void;
	error?: string | null;
	required?: boolean;
	placeholder?: string;
	disabled?: boolean;
	rows?: number;
	maxLength?: number;
	helpText?: string;
	className?: string;
}

export const ValidatedTextArea: React.FC< ValidatedTextAreaProps > = ( {
	label,
	value,
	onChange,
	onBlur,
	error,
	required,
	placeholder,
	disabled,
	rows = 4,
	maxLength,
	helpText,
	className,
} ) => {
	const remainingChars = maxLength ? maxLength - value.length : null;

	return (
		<FormField
			label={ label }
			required={ required }
			className={ className }
		>
			<div className="space-y-1">
				<textarea
					value={ value }
					onChange={ ( e ) => onChange( e.target.value ) }
					onBlur={ onBlur }
					placeholder={ placeholder }
					disabled={ disabled }
					rows={ rows }
					maxLength={ maxLength }
					className={ `
            w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-vertical
            ${
				error
					? 'border-red-500 focus:border-red-500 focus:ring-red-500'
					: 'border-gray-300'
			}
            ${ disabled ? 'bg-gray-100 cursor-not-allowed' : 'bg-white' }
          ` }
				/>

				<div className="flex justify-between items-start">
					<div className="flex-1">
						{ error && (
							<p className="text-sm text-red-600 flex items-center">
								<i className="fas fa-exclamation-triangle mr-1" />
								{ error }
							</p>
						) }

						{ helpText && ! error && (
							<p className="text-sm text-gray-500">
								{ helpText }
							</p>
						) }
					</div>

					{ maxLength && (
						<p
							className={ `text-xs ml-2 ${
								remainingChars !== null && remainingChars < 10
									? 'text-red-600'
									: 'text-gray-400'
							}` }
						>
							{ remainingChars } remaining
						</p>
					) }
				</div>
			</div>
		</FormField>
	);
};

interface ValidatedCheckboxProps {
	label: string;
	checked: boolean;
	onChange: ( checked: boolean ) => void;
	error?: string | null;
	disabled?: boolean;
	helpText?: string;
	className?: string;
	id?: string;
}

export const ValidatedCheckbox: React.FC< ValidatedCheckboxProps > = ( {
	label,
	checked,
	onChange,
	error,
	disabled,
	helpText,
	className,
	id,
} ) => {
	return (
		<div className={ `space-y-1 ${ className || '' }` }>
			<label
				htmlFor={ id }
				className={ `flex items-center ${
					disabled
						? 'cursor-not-allowed opacity-50'
						: 'cursor-pointer'
				}` }
			>
				<input
					id={ id }
					type="checkbox"
					checked={ checked }
					onChange={ ( e ) => onChange( e.target.checked ) }
					disabled={ disabled }
					className={ `
            mr-2 h-4 w-4 rounded border-gray-300 text-blue-600
            focus:ring-blue-500 focus:ring-2 focus:ring-offset-0
            ${ error ? 'border-red-500' : '' }
            ${ disabled ? 'cursor-not-allowed' : '' }
          ` }
				/>
				<span className="text-sm font-medium text-gray-700">
					{ label }
				</span>
			</label>

			{ error && (
				<p className="text-sm text-red-600 flex items-center ml-6">
					<i className="fas fa-exclamation-triangle mr-1" />
					{ error }
				</p>
			) }

			{ helpText && ! error && (
				<p className="text-sm text-gray-500 ml-6">{ helpText }</p>
			) }
		</div>
	);
};
