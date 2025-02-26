import "../css/kurss.css";
import "../css/tabula.css";
import axios from 'axios';
import { useEffect, useState } from 'preact/hooks';
import moment from 'moment';

export function Kursusaraksts() {
    const [stundasData, setStundasData] = useState([]);
    const [datums, setDatums] = useState(null);
    const [error, setError] = useState(null);
    const [loading, setLoading] = useState(true);
    const [laiksList, setLaiksList] = useState([]);
    const [kabinetsList, setKabinetsList] = useState([]);

    const queryString = window.location.search;
    console.log('Query String:', queryString);
    const queryParams = new URLSearchParams(queryString);
    const kurssName = queryParams.get('kurss');
    console.log('Query Params:', queryParams.toString());
    console.log('kurssName:', kurssName);

    useEffect(() => {
        console.log('useEffect triggered');
        console.log('kurssName inside useEffect:', kurssName);

        const fetchData = async () => {
            console.log('fetchData started');
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
                const [stundasResponse, laiksResponse, kabinetsResponse] = await Promise.all([
                    axios.get(`http://localhost:8000/api/ieplanotas-stundas?kurss=${encodeURIComponent(kurssName)}`, config),
                    axios.get(`http://localhost:8000/api/laiks`, config),
                    axios.get(`http://localhost:8000/api/kabinets`, config)
                ]);

                const data = stundasResponse.data || [];
                console.log('Stundas API Response:', data);
                setStundasData(data);

                if (data.length > 0 && data[0].datums) {
                    setDatums(data[0].datums);
                }

                const laiksData = laiksResponse.data || [];
                console.log('Laiks API Response:', laiksData);
                setLaiksList(laiksData);

                const kabinetsData = kabinetsResponse.data || [];
                console.log('Kabinets API Response:', kabinetsData);
                setKabinetsList(kabinetsData);

                setError(null);
            } catch (err) {
                console.error('API Error:', err.response?.data || err.message);
                setError(err.message || 'Failed to fetch data');
            } finally {
                setLoading(false);
                console.log('fetchData finished');
            }
        };

        if (kurssName) {
            fetchData();
        } else {
            console.log('kurssName is null or undefined');
        }
    }, [kurssName]);

    const getLaiksInfo = (laiksID) => {
        const laiks = laiksList.find(l => l.id === laiksID);
        if (laiks) {
            return {
                sakumalaiks: laiks.Sakumalaiks || laiks.sakumalaiks,
                beigulaiks: laiks.Beigulaiks || laiks.beigulaiks
            };
        }
        return {
            sakumalaiks: null,
            beigulaiks: null
        };
    };

    const getKabinetInfo = (kabinetaID) => {
        const kabinet = kabinetsList.find(k => k.id === kabinetaID);
        if (kabinet) {
            return {
                skaitlis: kabinet.Skaitlis || kabinet.skaitlis,
                vieta: kabinet.Vieta || kabinet.vieta
            };
        }
        return null;
    };

    const formatKabinetDisplay = (kabinetInfo) => {
        if (!kabinetInfo) return "Unknown Room";
        if (kabinetInfo.vieta === "Cēsis") {
            return `C. ${kabinetInfo.skaitlis}`;
        }
        return kabinetInfo.vieta ? 
            `${kabinetInfo.skaitlis} (${kabinetInfo.vieta})` : 
            kabinetInfo.skaitlis;
    };

    const groupByDayNumber = (data) => {
        const dayNames = ["Pirmdiena", "Otrdiena", "Trešdiena", "Ceturtdiena", "Piektdiena"];
        const groupedData = dayNames.map((day, index) => ({
            day,
            classes: data.filter(item => item.skaitlis === index + 1)
        }));
        return groupedData;
    };

    const groupedStundasData = groupByDayNumber(stundasData);

    const renderClasses = (classes) => {
        const maxClasses = 5;
        const renderedClasses = [];

        for (let i = 1; i <= maxClasses; i++) {
            const classItem = classes.find(item => item.laiksID === i);
            if (classItem) {
                const laiksInfo = getLaiksInfo(classItem.laiksID);
                const kabinetInfo = getKabinetInfo(classItem.kabinetaID);
                renderedClasses.push(
                    <div className="stundas" key={classItem.id}>
                        <div className="skaitlis">
                            {i}.
                        </div>
                        <div className="laiks">
                            {laiksInfo && laiksInfo.sakumalaiks ? 
                                moment(laiksInfo.sakumalaiks, 'HH:mm:ss').format('HH:mm') : 
                                'Unknown Start Time'} - 
                            {laiksInfo && laiksInfo.beigulaiks ? 
                                moment(laiksInfo.beigulaiks, 'HH:mm:ss').format('HH:mm') : 
                                'Unknown End Time'}
                        </div>
                        <div className="stunda">
                            {classItem.stunda ? classItem.stunda.Nosaukums : "Unknown Class"}
                        </div>
                        <div className="pasniedzejs">
                            {classItem.pasniedzejs ? `${classItem.pasniedzejs.Vards} ${classItem.pasniedzejs.Uzvards}` : "Unknown Teacher"}
                        </div>
                        <div className="kabinets">
                            {formatKabinetDisplay(kabinetInfo)}
                        </div>
                    </div>
                );
            } else {
                renderedClasses.push(
                    <div className="stundas" key={`empty-${i}`}>
                        <div className="skaitlis">
                            {i}.
                        </div>
                        <div className="laiks">
                            Nenotiek stunda
                        </div>
                    </div>
                );
            }
        }

        return renderedClasses;
    };

    return (
        <div className="kurssMain">
            {error && <div className="error-message">Error: {error}</div>}
            {loading ? (
                <p>Ielādē...</p>
            ) : (
                <>
                    <div className="header">
                        <h2>{kurssName} Nedēļas grafiks</h2>
                    </div>
                    {datums && (
                        <div className="datums">
                            <strong>Nedēļas datums: {moment(datums.PirmaisDatums).format('YYYY-MM-DD')} - {moment(datums.PedejaisDatums).format('YYYY-MM-DD')}</strong>
                        </div>
                    )}
                    <div className="kurssTable">
                        {stundasData.length === 0 ? (
                            <p>Nav ieplānotu stundu.</p>
                        ) : (
                            groupedStundasData.map((group, groupIndex) => (
                                <div key={groupIndex}>
                                    <h3>{group.day}</h3>
                                    {renderClasses(group.classes)}
                                </div>
                            ))
                        )}
                    </div>
                </>
            )}
        </div>
    );
}

export default Kursusaraksts;