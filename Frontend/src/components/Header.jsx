import { useLocation } from 'preact-iso';
import { useEffect, useState, useRef } from 'preact/hooks';
import axios from 'axios';
import { myDropdown, dropdownFunction, setupDropdownCloseListener, closeAllDropdowns } from './DropdownComponent.jsx';
import logo from '../assets/logo.svg';
import '../css/dropdown.css';
import '../css/style.css';

export function Header() {
    const [pasniedzejsData, setPasniedzejsData] = useState([]);
    const [kurssData, setKurssData] = useState([]);
    const [kabinetsData, setKabinetsData] = useState([]);
    const [error, setError] = useState(null);
    const [loading, setLoading] = useState(true);
    const [menuOpen, setMenuOpen] = useState(false);
    const [darkMode, setDarkMode] = useState(localStorage.getItem('darkMode') === 'true');

    const headerRef = useRef(null);
    const { url } = useLocation();

    useEffect(() => {
        const fetchData = async () => {
            const token = localStorage.getItem('token');
            const config = {
                headers: {
                    'Authorization': token ? `Bearer ${token}` : '',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            };

            setLoading(true);
            try {
                const [pasniedzejsResponse, kurssResponse, kabinetsResponse] = await Promise.all([
<<<<<<< Updated upstream
=======
                    // axios.get('https://api.markussv.id.lv/api/pasniedzejs', config),
                    // axios.get('https://api.markussv.id.lv/api/kurss', config),
                    // axios.get('https://api.markussv.id.lv/api/kabinets', config)
>>>>>>> Stashed changes
                    axios.get('http://localhost:8000/api/pasniedzejs', config),
                    axios.get('http://localhost:8000/api/kurss', config),
                    axios.get('http://localhost:8000/api/kabinets', config)
                ]);

                setPasniedzejsData(pasniedzejsResponse.data || []);
                setKurssData(kurssResponse.data || []);
                setKabinetsData(kabinetsResponse.data || []);
                setError(null);
            } catch (err) {
                console.error('API Error:', err.response?.data || err.message);
                setError(err.message || 'Failed to fetch data');
            } finally {
                setLoading(false);
            }
        };

        fetchData();
        setupDropdownCloseListener();

        // Add click outside listener
        const handleClickOutside = (event) => {
            if (headerRef.current && !headerRef.current.contains(event.target)) {
                setMenuOpen(false);
                closeAllDropdowns();
            }
        };

        document.addEventListener('mousedown', handleClickOutside);

        // Cleanup event listener
        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, []);

    useEffect(() => {
        if (darkMode) {
            document.body.classList.add('dark-mode');
        } else {
            document.body.classList.remove('dark-mode');
        }
        localStorage.setItem('darkMode', darkMode);
    }, [darkMode]);

    const toggleMenu = () => {
        setMenuOpen(!menuOpen);
        if (!menuOpen) {
            document.getElementById('burgerDropdown').classList.add('show');
        } else {
            closeAllDropdowns();
        }
    };

    const handleDropdownItemClick = () => {
        setMenuOpen(false);
        closeAllDropdowns();
    };

    const toggleDarkMode = () => {
        setDarkMode(!darkMode);
    };

    return (
        <header ref={headerRef}>
            <div className="header-logo">
                <img src={logo} className="image" alt="VTDT Logo" />
            </div>
            {error && <div className="error-message">Error: {error}</div>}
            <div className="burger-menu" onClick={toggleMenu}>
                &#9776;
            </div>
            <nav className={`user-navigation ${menuOpen ? 'show' : ''}`} id="burgerDropdown">
                <div className="dropdown">
                    <button onClick={() => myDropdown('dropdown1')} className="dropbtn">Kurss</button>
                    <div id="dropdown1" className="dropdown-content">
                        <input
                            type="text"
                            placeholder="Meklēt.."
                            id="myInput1"
                            className="myInput"
                            onKeyUp={() => dropdownFunction('myInput1', 'dropdown1')}
                        />
                        {loading ? (
                            <p>Ielādē...</p>
                        ) : kurssData.length === 0 ? (
                            <p>No courses available.</p>
                        ) : (
                            kurssData.map((item, index) => (
                                <a key={item.Nosaukums || item.id || index} href={`/kurss?kurss=${encodeURIComponent(item.Nosaukums)}`} onClick={handleDropdownItemClick}>
                                    {item.Nosaukums || "Unknown Course Name"}
                                </a>
                            ))
                        )}
                    </div>
                </div>

                <div className="dropdown">
                    <button onClick={() => myDropdown('dropdown2')} className="dropbtn">Pasniedzējs</button>
                    <div id="dropdown2" className="dropdown-content">
                        <input
                            type="text"
                            placeholder="Meklēt.."
                            id="myInput2"
                            className="myInput"
                            onKeyUp={() => dropdownFunction('myInput2', 'dropdown2')}
                        />
                        {loading ? (
                            <p>Ielādē...</p>
                        ) : pasniedzejsData.length === 0 ? (
                            <p>No teachers available.</p>
                        ) : (
                            pasniedzejsData.map((item, index) => (
                                <a key={`${item.Vards}-${item.Uzvards || index}`} href={`/pasniedzejs?name=${encodeURIComponent(item.Vards + ' ' + item.Uzvards)}`} onClick={handleDropdownItemClick}>
                                    {item.Vards && item.Uzvards ? `${item.Vards.charAt(0)}. ${item.Uzvards}` : "Unknown Teacher"}
                                </a>
                            ))
                        )}
                    </div>
                </div>

                <div className="dropdown">
                    <button onClick={() => myDropdown('dropdown3')} className="dropbtn">Kabinets</button>
                    <div id="dropdown3" className="dropdown-content">
                        <input
                            type="text"
                            placeholder="Meklēt.."
                            id="myInput3"
                            className="myInput"
                            onKeyUp={() => dropdownFunction('myInput3', 'dropdown3')}
                        />
                        {loading ? (
                            <p>Ielādē...</p>
                        ) : kabinetsData.length === 0 ? (
                            <p>No rooms available.</p>
                        ) : (
                            kabinetsData.map((item, index) => (
                                <a key={item.skaitlis || index} href={`/kabinets?number=${encodeURIComponent(item.skaitlis)}`} onClick={handleDropdownItemClick}>
                                    {item.vieta && item.skaitlis ? `${item.vieta.charAt(0)}. ${item.skaitlis}` : "Unknown Room"}
                                </a>
                            ))
                        )}
                    </div>
                </div>
                <div className="buttonContainer">
                    <a href="/" className={url === '/' ? 'active' : ''} style={{ fontWeight: 'bold' }}>
                        Stundu Laiki
                    </a>
                </div>
                <div className="buttonContainer">
                    <a onClick={toggleDarkMode} className="dark-mode-toggle">
                        {darkMode ? 'Light Mode' : 'Dark Mode'}
                    </a>
                </div>
            </nav>
        </header>
    );
}