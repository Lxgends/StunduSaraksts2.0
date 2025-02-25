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

            setLoading(true);
            try {
                // Adding more detailed error logging
                const pasniedzejsPromise = axios.get('http://localhost:8000/api/pasniedzejs', config)
                    .catch(err => {
                        console.error('Failed to fetch pasniedzejs:', err);
                        return { data: [] };
                    });
                
                const kurssPromise = axios.get('http://localhost:8000/api/kurss', config)
                    .catch(err => {
                        console.error('Failed to fetch kurss:', err);
                        return { data: [] };
                    });
                
                const kabinetsPromise = axios.get('http://localhost:8000/api/kabinets', config)
                    .catch(err => {
                        console.error('Failed to fetch kabinets:', err);
                        return { data: [] };
                    });

                const [pasniedzejsResponse, kurssResponse, kabinetsResponse] = await Promise.all([
                    pasniedzejsPromise,
                    kurssPromise,
                    kabinetsPromise
                ]);

                // Log the responses to help debug
                console.log('Pasniedzejs Response:', pasniedzejsResponse.data);
                console.log('Kurss Response:', kurssResponse.data);
                console.log('Kabinets Response:', kabinetsResponse.data);

                // Ensure we're working with arrays
                const pasniedzejsArray = Array.isArray(pasniedzejsResponse.data) ? pasniedzejsResponse.data : 
                                        (pasniedzejsResponse.data && Array.isArray(pasniedzejsResponse.data.data)) ? 
                                        pasniedzejsResponse.data.data : [];
                
                const kurssArray = Array.isArray(kurssResponse.data) ? kurssResponse.data : 
                                  (kurssResponse.data && Array.isArray(kurssResponse.data.data)) ? 
                                  kurssResponse.data.data : [];
                
                const kabinetsArray = Array.isArray(kabinetsResponse.data) ? kabinetsResponse.data : 
                                     (kabinetsResponse.data && Array.isArray(kabinetsResponse.data.data)) ? 
                                     kabinetsResponse.data.data : [];

                setPasniedzejsData(pasniedzejsArray);
                setKurssData(kurssArray);
                setKabinetsData(kabinetsArray);
                
                // Reset error state if successful
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
    }, []);
    
    console.log("Current state:", { pasniedzejsData, kurssData, kabinetsData, loading, error });

    return (
        <header>
            <img src={logo} className="image" alt="VTDT Logo" />
            {error && <div className="error-message">Error: {error}</div>}
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
                                kurssData.map((item, index) => (
                                    <a key={item.Nosaukums || item.id || index} href={`/kurss/${encodeURIComponent(item.Nosaukums || item.id)}`}>
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
                                    <a key={`${item.Vards}-${item.Uzvards || index}`} href={`/pasniedzejs/${encodeURIComponent(item.Vards)}`}>
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
                                    <a key={item.skaitlis || index} href={`/kabinets/${encodeURIComponent(item.skaitlis)}`}>
                                        {item.vieta && item.skaitlis ? `${item.vieta.charAt(0)}. ${item.skaitlis}` : "Unknown Room"}
                                    </a>
                                ))
                            )}
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