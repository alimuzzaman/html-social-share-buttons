import React from 'react';
import { FormFieldProps } from '../../types';

interface FormFieldWrapperProps extends FormFieldProps {
	children: React.ReactNode;
}

export const FormField: React.FC< FormFieldWrapperProps > = ( {
	label,
	description,
	required = false,
	error,
	className = '',
	children,
} ) => {
	return (
		<div className={ `mb-4 ${ className } ${ error ? 'has-error' : '' }` }>
			<div className="block text-sm font-medium text-gray-700 mb-1">
				<div className="block text-sm font-medium text-gray-700">
					{ label }
					{ required && <span className="text-red-500 ml-1">*</span> }
				</div>
			</div>

			<div className="block w-full">{ children }</div>

			{ description && (
				<p className="text-sm text-gray-600 mt-1">{ description }</p>
			) }

			{ error && <p className="text-sm text-red-600 mt-1">{ error }</p> }
		</div>
	);
};

interface TextInputProps {
	value: string;
	onChange: ( value: string ) => void;
	onBlur?: () => void;
	placeholder?: string;
	disabled?: boolean;
	type?: 'text' | 'email' | 'url' | 'password';
	className?: string;
}

export const TextInput: React.FC< TextInputProps > = ( {
	value,
	onChange,
	onBlur,
	placeholder,
	disabled = false,
	type = 'text',
	className = '',
} ) => {
	return (
		<input
			type={ type }
			value={ value }
			onChange={ ( e ) => onChange( e.target.value ) }
			onBlur={ onBlur }
			placeholder={ placeholder }
			disabled={ disabled }
			className={ `px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 ${ className }` }
		/>
	);
};

interface SelectProps {
	value: string;
	onChange: ( value: string ) => void;
	options: { value: string; label: string }[];
	disabled?: boolean;
	className?: string;
}

export const Select: React.FC< SelectProps > = ( {
	value,
	onChange,
	options,
	disabled = false,
	className = '',
} ) => {
	return (
		<select
			value={ value }
			onChange={ ( e ) => onChange( e.target.value ) }
			disabled={ disabled }
			className={ `px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 ${ className }` }
		>
			{ options.map( ( option ) => (
				<option key={ option.value } value={ option.value }>
					{ option.label }
				</option>
			) ) }
		</select>
	);
};

interface CheckboxProps {
	checked: boolean;
	onChange: ( checked: boolean ) => void;
	label: string;
	disabled?: boolean;
	className?: string;
	id?: string;
}

export const Checkbox: React.FC< CheckboxProps > = ( {
	checked,
	onChange,
	label,
	disabled = false,
	className = '',
	id,
} ) => {
	return (
		<label
			htmlFor={ id }
			className={ `flex items-center cursor-pointer space-x-2 ${ className }` }
		>
			<input
				id={ id }
				type="checkbox"
				checked={ checked }
				onChange={ ( e ) => onChange( e.target.checked ) }
				disabled={ disabled }
				className="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
			/>
			<span className="text-sm text-gray-700">{ label }</span>
		</label>
	);
};

interface ButtonProps {
	onClick: () => void;
	children: React.ReactNode;
	variant?: 'primary' | 'secondary' | 'tertiary';
	size?: 'small' | 'medium' | 'large';
	disabled?: boolean;
	loading?: boolean;
	className?: string;
}

export const Button: React.FC< ButtonProps > = ( {
	onClick,
	children,
	variant = 'primary',
	size = 'medium',
	disabled = false,
	loading = false,
	className = '',
} ) => {
	const getButtonClasses = () => {
		let classes =
			'px-4 py-2 rounded font-medium transition-colors focus:outline-none focus:ring-1 focus:ring-blue-500';

		// Variant classes
		switch ( variant ) {
			case 'primary':
				classes +=
					' bg-blue-600 text-white hover:bg-blue-700 disabled:bg-blue-400';
				break;
			case 'secondary':
				classes +=
					' bg-white border border-gray-300 text-gray-900 hover:bg-gray-50 disabled:bg-gray-100 disabled:text-gray-400';
				break;
			case 'tertiary':
				classes +=
					' bg-transparent text-blue-600 hover:bg-blue-50 disabled:text-blue-400';
				break;
		}

		// Size classes
		switch ( size ) {
			case 'small':
				classes += ' px-3 py-1.5 text-sm';
				break;
			case 'large':
				classes += ' px-6 py-3 text-lg';
				break;
			case 'medium':
			default:
				// Default size already included
				break;
		}

		return `${ classes } ${ className }`;
	};

	return (
		<button
			type="button"
			onClick={ onClick }
			disabled={ disabled || loading }
			className={ getButtonClasses() }
		>
			{ loading && (
				<svg
					className="animate-spin -ml-1 mr-2 h-4 w-4 text-current inline-block"
					xmlns="http://www.w3.org/2000/svg"
					fill="none"
					viewBox="0 0 24 24"
				>
					<circle
						className="opacity-25"
						cx="12"
						cy="12"
						r="10"
						stroke="currentColor"
						strokeWidth="4"
					></circle>
					<path
						className="opacity-75"
						fill="currentColor"
						d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
					></path>
				</svg>
			) }
			{ children }
		</button>
	);
};
