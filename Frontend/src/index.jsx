import { LocationProvider, Router, Route, hydrate, prerender as ssr, useLocation } from 'preact-iso';
import { Header } from './components/Header.jsx';
import { Footer } from './components/Footer.jsx';
import Kursusaraksts from './pages/kursusaraksts.jsx';
import Pasniedzejs from './pages/pasniedzejs.jsx';
import Kabinets from './pages/kabinets.jsx';
import Laiki from './pages/laiki.jsx';
import { NotFound } from './pages/_404.jsx';
import './css/style.css';
import axios from 'axios';
import { useEffect } from 'preact/hooks';

axios.defaults.withCredentials = true;

if (typeof window !== 'undefined') {
    window.__hasRedirected = window.__hasRedirected || false;
}

export function App() {
    useEffect(() => {
        if (typeof window === 'undefined') return;

        function saveCurrentPage() {
            const fullPath = window.location.pathname + window.location.search;
            if (fullPath !== '/' && fullPath !== '') {
                console.log('Saving page to localStorage:', fullPath);
                localStorage.setItem('lastVisitedPage', fullPath);
            }
        }

        document.addEventListener('visibilitychange', saveCurrentPage);

        document.addEventListener('click', () => {
            setTimeout(saveCurrentPage, 100);
        });

        if ((window.location.pathname === '/' || window.location.pathname === '') && !window.__hasRedirected) {
            const lastVisitedPage = localStorage.getItem('lastVisitedPage');
            if (lastVisitedPage) {
                console.log('Redirecting to:', lastVisitedPage);
                window.__hasRedirected = true;
                window.location.href = lastVisitedPage;
            }
        }

        saveCurrentPage();

        return () => {
            document.removeEventListener('visibilitychange', saveCurrentPage);
            document.removeEventListener('click', saveCurrentPage);
        };
    }, []);
    
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
    window.addEventListener('DOMContentLoaded', () => {
        const fullPath = window.location.pathname + window.location.search;
        if (fullPath !== '/' && fullPath !== '') {
            localStorage.setItem('lastVisitedPage', fullPath);
        }
        
        let lastUrl = window.location.href;
        setInterval(() => {
            const currentUrl = window.location.href;
            if (currentUrl !== lastUrl) {
                lastUrl = currentUrl;
                const fullPath = window.location.pathname + window.location.search;
                if (fullPath !== '/' && fullPath !== '') {
                    console.log('URL changed, saving:', fullPath);
                    localStorage.setItem('lastVisitedPage', fullPath);
                }
            }
        }, 500);
    });
}

if (typeof window !== 'undefined') {
    hydrate(<App />, document.getElementById('app'));
}

export async function prerender(data) {
    return await ssr(<App {...data} />);
}