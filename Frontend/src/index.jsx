import { LocationProvider, Router, Route, hydrate, prerender as ssr } from 'preact-iso';
import { Header } from './components/Header.jsx';
import { Footer } from './components/Footer.jsx';
import Kursusaraksts from './pages/kursusaraksts.jsx';
import Pasniedzejs from './pages/pasniedzejs.jsx';
import Kabinets from './pages/kabinets.jsx';
import Laiki from './pages/laiki.jsx';
import { NotFound } from './pages/_404.jsx';
import './css/style.css';
import axios from 'axios';

axios.defaults.withCredentials = true;

export function App() {
	return (
		<LocationProvider>
			<Header />
			<main>
				<Router>
					<Route path="/" component={Laiki} />
					<Route path="/pasniedzejs" component={Pasniedzejs} />
					<Route path="/kabinets" component={Kabinets} />
					<Route path="/kurss" component={Kursusaraksts} />
					<Route default component={NotFound} />
				</Router>
			</main>
			<Footer />
		</LocationProvider>

	);
}

if (typeof window !== 'undefined') {
	hydrate(<App />, document.getElementById('app'));
}

export async function prerender(data) {
	return await ssr(<App {...data} />);
}
