import { LocationProvider, Router, Route, hydrate, prerender as ssr } from 'preact-iso';
import { Header } from './components/Header.jsx';
import { Footer } from './components/Footer.jsx';
import KursuSaraksts from './pages/KursuSaraksts.jsx';
import Pasniedzejs from './pages/pasniedzejs.jsx';
import Kabinets from './pages/kabinets.jsx';
import Laiki from './pages/laiki.jsx';
import Bottom from "./components/Bottom.jsx"
import { NotFound } from './pages/_404.jsx';
import './css/style.css';

export function App() {
	return (
		<LocationProvider>
			<Header />
			<main>
				<Router>
					<Route path="/" component={KursuSaraksts} />
					<Route path="/pasniedzejs" component={Pasniedzejs} />
					<Route path="/kabinets" component={Kabinets} />
					<Route path="/laiki" component={Laiki} />
					<Route default component={NotFound} />
				</Router>
				<Footer />
			</main>
			<Bottom />
		</LocationProvider>

	);
}

if (typeof window !== 'undefined') {
	hydrate(<App />, document.getElementById('app'));
}

export async function prerender(data) {
	return await ssr(<App {...data} />);
}
