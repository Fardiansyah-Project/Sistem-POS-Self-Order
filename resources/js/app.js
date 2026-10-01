import $ from 'jquery';
// import './bootstrap';
import Chart from 'chart.js/auto';

// Membuat Chart.js bisa diakses secara global (opsional)
window.Chart = Chart;

window.$ = window.jQuery = $;

const adminScriptsTemplate = document.querySelector('#admin-page-scripts');

if (adminScriptsTemplate) {
	const appendScript = (source) => new Promise((resolve, reject) => {
		const script = document.createElement('script');
		script.async = false;

		for (const attribute of source.attributes) {
			script.setAttribute(attribute.name, attribute.value);
		}

		script.textContent = source.textContent;

		if (source.src) {
			script.onload = resolve;
			script.onerror = () => reject(new Error(`Gagal memuat ${source.src}`));
		}

		document.body.append(script);

		if (!source.src) resolve();
	});

	const loadAdminScripts = async () => {
		try {
			const helpers = document.createElement('script');
			helpers.src = '/js/admin/app.js';
			await appendScript(helpers);

			for (const script of adminScriptsTemplate.content.querySelectorAll('script')) {
				await appendScript(script);
			}

			adminScriptsTemplate.remove();
		} catch (error) {
			console.error(error);
		}
	};

	loadAdminScripts();
}
