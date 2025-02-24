import { defineConfig } from 'vite';
import preact from '@preact/preset-vite';

// https://vitejs.dev/config/
export default defineConfig({
	plugins: [
		preact({
			server: {
				proxy: {
					'/api': {
						target: 'http://localhost:8000',
						changeOrigin: true,
						rewrite: (path) => path.replace(/^\/api/, ''),
						prerender: {}
					}
				},
				enabled: true,
				renderTarget: '#app',
				additionalPrerenderRoutes: ['/404'],
				previewMiddlewareEnabled: true,
				previewMiddlewareFallback: '/404',
			},
		}),
	],
});
