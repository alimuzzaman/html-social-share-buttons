import { createRoot } from 'react-dom/client';
import { MainAdminInterface } from './components/MainAdminInterface';

// Demo entry point - shows how to use the complete admin interface
const container = document.getElementById( 'html-social-share-admin' );
if ( container ) {
	const root = createRoot( container );
	root.render( <MainAdminInterface /> );
}
