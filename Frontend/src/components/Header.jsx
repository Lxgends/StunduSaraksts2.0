import { useLocation } from 'preact-iso';
import { useEffect, useState } from 'preact/hooks';
import axios from 'axios';
import { myDropdown, dropdownFunction, setupDropdownCloseListener } from './DropdownComponent.jsx';
import logo from '../assets/logo.svg';
import '../css/dropdown.css';

export function Header() {
    const [pasniedzejsData, setPasniedzejsData] = useState([]);
    const [kurssData, setKurssData] = useState([]);
    const [kabinetsData, setKabinetsData] = useState([]);
    const [error, setError] = useState(null);
    const [loading, setLoading] = useState(true);

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

            try {
                const [pasniedzejsResponse, kurssResponse, kabinetsResponse] = await Promise.all([
                    axios.get('http://localhost:8000/api/pasniedzejs', config),
                    axios.get('http://localhost:8000/api/kurss', config),
                    axios.get('http://localhost:8000/api/kabinets', config),
                ]);

                setPasniedzejsData(Array.isArray(pasniedzejsResponse.data) ? pasniedzejsResponse.data : []);
                setKurssData(Array.isArray(kurssResponse.data) ? kurssResponse.data : []);
                setKabinetsData(Array.isArray(kabinetsResponse.data) ? kabinetsResponse.data : []);
            } catch (err) {
                console.error('API Error:', err.response?.data || err.message);
                setError(err.message);
            } finally {
                setLoading(false);
            }
        };

        fetchData();
        setupDropdownCloseListener();
    }, []);

    return (
        <header>
            <img src={logo} className="image" alt="VTDT Logo" />
            <nav>
                <div className="user-navigation">
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
                                kurssData.map((item) => (
                                    <a key={item.Nosaukums || item.id} href={`/kurss?name=${item.Nosaukums || item.id}`}>
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
                            ) : pasniedzejsData.map((item) => (
                                <a key={`${item.Vards}-${item.Uzvards}`} href={`/pasniedzejs?=${item.Vards}`}>
                                    {`${item.Vards.charAt(0)}. ${item.Uzvards}`}
                                </a>
                            ))}
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
                            ) : kabinetsData.map((item) => (
                                <a key={item.Skaitlis} href={`/kabinets?=${item.Skaitlis}`}>
                                    {`${item.Vieta.charAt(0)}. ${item.Skaitlis}`}
                                </a>
                            ))}
                        </div>
                    </div>
                    <a href="/" className={url === '/' ? 'active' : ''} style={{ fontWeight: 'bold' }}>
                        Stundu Laiki
                    </a>
                </div>
            </nav>
        </header>
    );
}
