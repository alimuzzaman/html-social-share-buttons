import React from 'react';
import { NoticeProps } from '../../types';

export const Notice: React.FC< NoticeProps > = ( {
	type,
	message,
	dismissible = false,
	onDismiss,
} ) => {
	const getNoticeClasses = () => {
		const baseClasses = 'p-4 border-l-4 bg-white rounded-r shadow-sm';

		switch ( type ) {
			case 'success':
				return `${ baseClasses } border-green-500 bg-green-50`;
			case 'warning':
				return `${ baseClasses } border-yellow-500 bg-yellow-50`;
			case 'error':
				return `${ baseClasses } border-red-500 bg-red-50`;
			case 'info':
			default:
				return `${ baseClasses } border-blue-500 bg-blue-50`;
		}
	};

	return (
		<div
			className={ `${ getNoticeClasses() } ${
				dismissible ? 'relative' : ''
			}` }
		>
			<div className="flex items-start">
				<div className="flex-1">
					<p className="text-sm font-medium">{ message }</p>
				</div>
				{ dismissible && onDismiss && (
					<button
						type="button"
						className="ml-4 p-1 rounded hover:bg-black hover:bg-opacity-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
						onClick={ onDismiss }
						aria-label="Dismiss this notice"
					>
						<span className="sr-only">Dismiss this notice</span>
						<svg
							className="w-4 h-4"
							fill="none"
							stroke="currentColor"
							viewBox="0 0 24 24"
						>
							<path
								strokeLinecap="round"
								strokeLinejoin="round"
								strokeWidth={ 2 }
								d="M6 18L18 6M6 6l12 12"
							/>
						</svg>
					</button>
				) }
			</div>
		</div>
	);
};
