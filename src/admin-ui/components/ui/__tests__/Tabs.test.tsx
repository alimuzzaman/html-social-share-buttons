import React from 'react';
import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import '@testing-library/jest-dom';

import { Tabs, TabPanel } from '../Tabs';

const tabsData = [
	{ id: 'display', title: 'Display' },
	{ id: 'appearance', title: 'Appearance' },
];

describe('Tabs component', () => {
	test('renders tab buttons and highlights the active tab', () => {
		const onTabChange = jest.fn();
		render(<Tabs tabs={tabsData} activeTab="display" onTabChange={onTabChange} />);

		const displayTab = screen.getByRole('tab', { name: /Display/i });
		expect(displayTab).toBeInTheDocument();
		expect(displayTab).toHaveAttribute('aria-selected', 'true');
		expect(displayTab).toHaveClass('border-b-4');
	
		const appearanceTab = screen.getByRole('tab', { name: /Appearance/i });
		expect(appearanceTab).toHaveAttribute('aria-selected', 'false');
	});

	test('clicking a tab calls onTabChange with the tab id', async () => {
		const user = userEvent.setup();
		const onTabChange = jest.fn();
		render(<Tabs tabs={tabsData} activeTab="display" onTabChange={onTabChange} />);

		const appearanceTab = screen.getByRole('tab', { name: /Appearance/i });
		await user.click(appearanceTab);
		expect(onTabChange).toHaveBeenCalledWith('appearance');
	});

	test('TabPanel only renders when active', () => {
		render(
			<>
				<TabPanel id="display" activeTab="display">
					<div>Display content</div>
				</TabPanel>
				<TabPanel id="appearance" activeTab="display">
					<div>Appearance content</div>
				</TabPanel>
			</>
		);

		expect(screen.getByText('Display content')).toBeInTheDocument();
		expect(screen.queryByText('Appearance content')).toBeNull();
	});

	test('active tab uses label-level underline and left-aligns content', async () => {
		const onTabChange = jest.fn();
		render(<Tabs tabs={tabsData} activeTab="display" onTabChange={onTabChange} />);

		const displayTab = screen.getByRole('tab', { name: /Display/i });
		// expect the button inner content to be left-aligned (justify-start) so underline sits under label
		const displayInner = displayTab.querySelector('div');
		expect(displayInner).toHaveClass('justify-start');

		// expect underline to be applied to the label element rather than entire button
		const labelSpan = displayTab.querySelector('span:not([class*="mr-"])');
		expect(labelSpan).toHaveClass('border-b-4');
	});
});
