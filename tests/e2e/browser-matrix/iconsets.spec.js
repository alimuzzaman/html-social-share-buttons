const fs = require( 'fs' );
const path = require( 'path' );
const { test, expect } = require( '@playwright/test' );
const { createPublishedPage, login } = require( '../helpers/wordpress' );

const cells = [
	{ id: 'default-square', iconset: 'default', shape: 'square', links: 7 },
	{ id: 'flat-square', iconset: 'flat', shape: 'square', links: 7 },
	{ id: 'flat-circle', iconset: 'flat', shape: 'circle', links: 7 },
	{ id: 'long-shadows-square', iconset: 'long-shadows', shape: 'square', links: 7 },
	{ id: 'long-shadows-circle', iconset: 'long-shadows', shape: 'circle', links: 7 },
	{ id: 'prajin-square', iconset: 'prajin', shape: 'square', links: 7 },
	{ id: 'prajin-circle', iconset: 'prajin', shape: 'circle', links: 7 },
	{ id: 'bootstrap-solid-square', iconset: 'bootstrap-solid', shape: 'square', links: 7 },
	{ id: 'bootstrap-solid-circle', iconset: 'bootstrap-solid', shape: 'circle', links: 7 },
	{ id: 'tabler-outline-square', iconset: 'tabler-outline', shape: 'square', links: 7 },
	{ id: 'tabler-outline-circle', iconset: 'tabler-outline', shape: 'circle', links: 7 },
];

const networks = 'facebook,x,linkedin,pinterest,telegram,bluesky,mail';

function fixtureContent() {
	return cells
		.map(
			( cell ) =>
				`<section class="hssb-browser-matrix-cell" data-hssb-cell="${ cell.id }"><h2>${ cell.id }</h2>[zm_sh_btn title="${ cell.id }" iconset="${ cell.iconset }" iconset_type="${ cell.shape }" icons="${ networks }"]</section>`
		)
		.join( '\n' );
}

function screenshotPath( testInfo ) {
	const configuredRoot = process.env.HSSB_BROWSER_ARTIFACT_DIR;
	const filename = `${ testInfo.project.name }.png`;

	if ( ! configuredRoot ) {
		return testInfo.outputPath( filename );
	}

	const output = path.resolve( process.cwd(), configuredRoot, filename );
	fs.mkdirSync( path.dirname( output ), { recursive: true } );
	return output;
}

test.describe( 'Icon-set browser matrix', () => {
	test( 'renders every declared icon-set and shape cell', async ( {
		page,
	}, testInfo ) => {
		await login( page );
		const fixture = await createPublishedPage( page, {
			content: fixtureContent(),
			title: `HSSB browser matrix ${ testInfo.project.name }`,
		} );

		await page.goto( fixture.link );

		for ( const cell of cells ) {
			const section = page.locator(
				`[data-hssb-cell="${ cell.id }"]`
			);
			const wrapper = section.locator( '.zmshbt.in_shortcode' );
			await expect( section ).toBeVisible();
			await expect( wrapper ).toBeVisible();
			await expect( wrapper ).toHaveClass(
				new RegExp( `\\b${ cell.iconset }\\b` )
			);
			await expect( wrapper ).toHaveClass(
				new RegExp( `\\b${ cell.shape }\\b` )
			);
			await expect(
				wrapper.locator( 'a:not(.zmshbt-profile-link)' )
			).toHaveCount( cell.links );
			await expect(
				wrapper.locator( 'a.facebook:not(.zmshbt-profile-link)' )
			).toHaveCSS(
				'background-image',
				/url\(.+\)/
			);
		}

		if ( testInfo.project.name.endsWith( 'mobile-viewport' ) ) {
			const pageHeading = page.locator( 'main h1' ).first();
			const rails = page.locator( '.zmshbt.left, .zmshbt.right' );
			await expect( pageHeading ).toBeVisible();
			expect( await rails.count() ).toBeGreaterThan( 0 );

			const headingBox = await pageHeading.boundingBox();
			for ( const rail of await rails.all() ) {
				await expect( rail ).toHaveCSS( 'position', 'static' );
				const railBox = await rail.boundingBox();
				expect( headingBox ).not.toBeNull();
				expect( railBox ).not.toBeNull();
				if ( ! headingBox || ! railBox ) {
					continue;
				}
				expect(
					railBox.x < headingBox.x + headingBox.width &&
						railBox.x + railBox.width > headingBox.x &&
						railBox.y < headingBox.y + headingBox.height &&
						railBox.y + railBox.height > headingBox.y
				).toBeFalsy();
			}
		}

		const output = screenshotPath( testInfo );
		await page.screenshot( { path: output, fullPage: true } );
		await testInfo.attach( 'icon-set-matrix', {
			path: output,
			contentType: 'image/png',
		} );
	} );
} );

// Isolated shipped-CSS proof; this does not replace the WordPress matrix above.
test("core auto-hide uses each pack width and reveals keyboard focus", async ({
  page,
  browser,
  browserName,
}) => {
  test.setTimeout(180_000);
  const touchContext = await browser.newContext({
    hasTouch: true,
    viewport: { width: 1200, height: 800 },
  });
  const touchPage = await touchContext.newPage();
  const root = path.resolve(__dirname, "../../..");
  const packs = {
    default: "iconset/default/style.css",
    flat: "iconset/flat/style.css",
    "long-shadows": "iconset/long_shadow/style.css",
    prajin: "iconset/prajin/style.css",
    "bootstrap-solid": "assets/iconsets/bootstrap-solid/style.css",
    "tabler-outline": "assets/iconsets/tabler-outline/style.css",
  };
  const core = fs.readFileSync(
    path.join(root, "assets/frontend/button-appearance.css"),
    "utf8",
  );
  await page.setViewportSize({ width: 1200, height: 800 });
  await page.emulateMedia({ reducedMotion: "no-preference" });
  for (const [pack, file] of Object.entries(packs)) {
    const packCSS = fs.readFileSync(path.join(root, file), "utf8");
    for (const appearance of ["legacy", "minimal", "framed", "soft-shadow"]) {
      for (const side of ["left", "right"]) {
        await page.setContent(
          `<style>${packCSS}${core}</style><button id="before">Before</button><div class="zmshbt ${pack} square ${side} ${
            appearance === "legacy" ? "" : `hssb-appearance--${appearance}`
          } hssb-rail--auto-hide"><a href="#one">One</a><a href="#two">Two</a></div><button id="after">After</button>`,
        );
        await touchPage.setContent(await page.content());
        await expect(touchPage.locator(".zmshbt")).toHaveCSS(
          "transform",
          "none",
        );
        await page.mouse.move(600, 700);
        const rail = page.locator(".zmshbt");
        const visibleWidth = () =>
          rail.evaluate((el) => {
            const rect = el.getBoundingClientRect();
            return Math.min(innerWidth, rect.right) - Math.max(0, rect.left);
          });
        await expect.poll(visibleWidth).toBeCloseTo(12, 0);
        const hiddenBox = await rail.boundingBox();
        await page.mouse.move(side === "left" ? 6 : 1194, hiddenBox.y + 10);
        await expect.poll(() => rail.evaluate((el) => {
          const rect = el.getBoundingClientRect();
          const visible = Math.min(innerWidth, rect.right) - Math.max(0, rect.left);
          return rect.width - visible;
        })).toBeCloseTo(0, 0);
        await page.mouse.move(600, 700);
        await page.locator("#before").focus();
        await page.keyboard.press(browserName === "webkit" && process.platform === "darwin" ? "Alt+Tab" : "Tab");
        await expect(rail.locator("a").first()).toBeFocused();
        await expect.poll(() => rail.evaluate((el) => {
          const rect = el.getBoundingClientRect();
          const visible = Math.min(innerWidth, rect.right) - Math.max(0, rect.left);
          return rect.width - visible;
        })).toBeCloseTo(0, 0);
        await page.locator("#after").focus();
        await page.keyboard.press(browserName === "webkit" && process.platform === "darwin" ? "Alt+Shift+Tab" : "Shift+Tab");
        await expect(rail.locator("a").last()).toBeFocused();
        await expect.poll(() => rail.evaluate((el) => {
          const rect = el.getBoundingClientRect();
          const visible = Math.min(innerWidth, rect.right) - Math.max(0, rect.left);
          return rect.width - visible;
        })).toBeCloseTo(0, 0);
        await page.locator("#before").focus();
        await page.emulateMedia({ reducedMotion: "reduce" });
        await expect(rail).toHaveCSS("transform", "none");
        await expect(rail).toHaveCSS("transition-duration", "0s");
        await page.emulateMedia({ reducedMotion: "no-preference" });
        for (const width of [390, 600]) {
          await page.setViewportSize({ width, height: 800 });
          await expect(rail).toHaveCSS("transform", "none");
          expect(
            await page.evaluate(
              () => document.documentElement.scrollWidth <= innerWidth,
            ),
          ).toBeTruthy();
        }
        await page.setViewportSize({ width: 1200, height: 800 });
        await rail.evaluate((el) =>
          el.classList.remove("hssb-rail--auto-hide"),
        );
        await page.addStyleTag({
          content:
            ".zmshbt.left{left:0!important}.zmshbt.right{right:0!important}",
        });
        await expect(rail).toHaveCSS("transform", "none");
        await expect.poll(() => rail.evaluate((el) => {
          const rect = el.getBoundingClientRect();
          const visible = Math.min(innerWidth, rect.right) - Math.max(0, rect.left);
          return rect.width - visible;
        })).toBeCloseTo(0, 0);
      }
    }
  }
  await touchContext.close();
});

// Exercise the installed plugin's settings, renderer and enqueued styles together.
test( 'installed WordPress rails honor saved auto-hide and every appearance', async ( { page } ) => {
	test.setTimeout( 180_000 );
	await login( page );
	const fixture = await createPublishedPage( page, {
		content: '<p>Installed candidate rail fixture.</p>',
		title: 'Installed candidate automatic rails',
	} );
	const settingsUrl = '/wp-admin/options-general.php?page=zm_shbt_opt';
	const appearance = () => page.locator( '#button_appearance' );
	const autoHide = () => page.locator( 'input[name="zm_shbt_fld[auto_hide_btn]"]' );
	const left = () => page.locator( 'input[name="zm_shbt_fld[show_in][show_left]"]' );
	const right = () => page.locator( 'input[name="zm_shbt_fld[show_in][show_right]"]' );
	await page.goto( settingsUrl );
	const original = {
		appearance: await appearance().inputValue(),
		autoHide: await autoHide().isChecked(),
		left: await left().isChecked(),
		right: await right().isChecked(),
	};
	const save = async () => {
		const response = page.waitForResponse( ( candidate ) =>
			candidate.url().includes( '/wp-admin/admin-ajax.php' ) &&
			candidate.request().postData()?.includes( 'zm_sh_save_settings' )
		);
		await page.locator( '#submit' ).click();
		expect( ( await response ).ok() ).toBeTruthy();
		await expect( page.locator( '.components-snackbar__content' ).getByText( 'Settings saved.', { exact: true } ) ).toBeVisible();
	};
	try {
		for ( const value of [ 'legacy', 'minimal', 'framed', 'soft-shadow' ] ) {
			for ( const enabled of [ true, false ] ) {
				await page.goto( settingsUrl );
				await appearance().selectOption( value );
				await autoHide().setChecked( enabled );
				await left().check();
				await right().check();
				await save();
				await page.reload();
				await expect( appearance() ).toHaveValue( value );
				if ( enabled ) {
					await expect( autoHide() ).toBeChecked();
				} else {
					await expect( autoHide() ).not.toBeChecked();
				}
				await page.goto( fixture.link );
				await page.mouse.move( page.viewportSize().width / 2, 1 );
				if ( enabled || value !== 'legacy' ) {
					await expect( page.locator( 'link#hssb-button-appearance-css' ) ).toHaveAttribute( 'href', /\/assets\/frontend\/button-appearance\.css/ );
				}
				for ( const side of [ 'left', 'right' ] ) {
					const rail = page.locator( `.zmshbt.${ side }` );
					await expect( rail ).toHaveCount( 1 );
					if ( enabled ) {
						await expect( rail ).toHaveClass( /\bhssb-rail--auto-hide\b/ );
					} else {
						await expect( rail ).not.toHaveClass( /\bhssb-rail--auto-hide\b/ );
					}
					if ( value === 'legacy' ) {
						await expect( rail ).not.toHaveClass( /hssb-appearance--/ );
					} else {
						await expect( rail ).toHaveClass( new RegExp( `\\bhssb-appearance--${ value }\\b` ) );
					}
					const visibleWidth = () => rail.evaluate( ( element ) => {
						const box = element.getBoundingClientRect();
						return Math.min( innerWidth, box.right ) - Math.max( 0, box.left );
					} );
					if ( enabled && page.viewportSize().width > 600 ) {
						await expect.poll( visibleWidth ).toBeCloseTo( 12, 0 );
					} else {
						await expect( rail ).toHaveCSS( 'transform', 'none' );
					}
					await rail.locator( 'a' ).first().focus();
					await expect.poll( async () => Math.round( await visibleWidth() ) ).toBe( Math.round( ( await rail.boundingBox() ).width ) );
					await rail.locator( 'a' ).first().evaluate( ( element ) => element.blur() );
				}
			}
		}
	} finally {
		await page.goto( settingsUrl );
		await appearance().selectOption( original.appearance );
		await autoHide().setChecked( original.autoHide );
		await left().setChecked( original.left );
		await right().setChecked( original.right );
		await save();
	}
} );
