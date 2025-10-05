import React from 'react';
import { TabConfig } from '../../types';

interface TabsProps {
	tabs: TabConfig[];
	activeTab: string;
	onTabChange: ( tabId: string ) => void;
	className?: string;
}

export const Tabs: React.FC< TabsProps > = ( {
	tabs,
	activeTab,
	onTabChange,
	className = '',
} ) => {
	return (
		<div className={ className }>
			<div className="mb-6" role="tablist" aria-orientation="horizontal">
				<div className="flex space-x-1 border-b border-gray-200">
					{ tabs.map( ( tab ) => (
						<button
							key={ tab.id }
							id={ `tab-${ tab.id }` }
							role="tab"
							aria-selected={ activeTab === tab.id }
							aria-controls={ `panel-${ tab.id }` }
							className={ `px-6 py-3 text-sm font-medium transition-colors focus:outline-none -mb-0.5 ${
								activeTab === tab.id
									? 'text-blue-600 border-b-4 border-blue-600 bg-white'
									: 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'
							}` }
							onClick={ () => onTabChange( tab.id ) }
						>
							<div className="flex items-center justify-start">
								{ tab.icon && (
									<span className="mr-2" aria-hidden="true">
										{ tab.icon }
									</span>
								) }
								<span
									className={ `${
										activeTab === tab.id
											? 'border-b-4 border-blue-600 pb-1'
											: ''
									}` }
								>
									{ tab.title }
								</span>
							</div>
						</button>
					) ) }
				</div>
			</div>
		</div>
	);
};

export const VerticalTabs: React.FC< TabsProps > = ( {
	tabs,
	activeTab,
	onTabChange,
	className = '',
} ) => {
	return (
		<div className={ `flex ${ className }` }>
			<div
				className="w-64 border-r border-gray-200 pr-4"
				role="tablist"
				aria-orientation="vertical"
			>
				<div className="flex flex-col space-y-1">
					{ tabs.map( ( tab ) => (
						<button
							key={ tab.id }
							id={ `vtab-${ tab.id }` }
							role="tab"
							aria-selected={ activeTab === tab.id }
							aria-controls={ `vpanel-${ tab.id }` }
							className={ `px-4 py-3 text-sm font-medium rounded-md transition-colors focus:outline-none text-left flex items-center ${
								activeTab === tab.id
									? 'text-blue-600 bg-blue-50 border-r-4 border-blue-600'
									: 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'
							}` }
							onClick={ () => onTabChange( tab.id ) }
						>
							{ tab.icon && (
								<span
									className="mr-2 flex-shrink-0"
									aria-hidden="true"
								>
									{ tab.icon }
								</span>
							) }
							<span
								className={ `${
									activeTab === tab.id
										? 'border-r-4 border-blue-600 pr-1'
										: ''
								} truncate` }
							>
								{ tab.title }
							</span>
						</button>
					) ) }
				</div>
			</div>
			<div className="flex-1 pl-6">
				{ /* Content comes from parent TabPanel rendering */ }
			</div>
		</div>
	);
};

interface TabPanelProps {
	id: string;
	activeTab: string;
	children: React.ReactNode;
	className?: string;
}

export const TabPanel: React.FC< TabPanelProps > = ( {
	id,
	activeTab,
	children,
	className = '',
} ) => {
	if ( activeTab !== id ) {
		return null;
	}

	return (
		<div
			role="tabpanel"
			aria-labelledby={ `tab-${ id }` }
			id={ `panel-${ id }` }
			className={ `mt-6 ${ className }` }
		>
			{ children }
		</div>
	);
};
