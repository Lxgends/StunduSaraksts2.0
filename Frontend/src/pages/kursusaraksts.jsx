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
    const [datumaID, setDatumaID] = useState(null);
    const [allDatums, setAllDatums] = useState([]);

    const queryString = window.location.search;
    const queryParams = new URLSearchParams(queryString);
    const kurssName = queryParams.get('kurss');

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
                const [stundasResponse, laiksResponse, kabinetsResponse, datumsResponse] = await Promise.all([
                    axios.get(`https://api.markussv.id.lv/api/ieplanotas-stundas?kurss=${encodeURIComponent(kurssName)}&datumsID=${datumaID}`, config),
                    axios.get(`https://api.markussv.id.lv/api/laiks`, config),
                    axios.get(`https://api.markussv.id.lv/api/kabinets`, config),
                    axios.get(`https://api.markussv.id.lv/api/datums`, config)
                ]);

                const data = stundasResponse.data || [];
                setStundasData(data);

                const datumsData = datumsResponse.data || [];
                setAllDatums(datumsData);

                if (!datumaID && datumsData.length > 0) {
                    const latestDatumaID = Math.max(...datumsData.map(d => d.id));
                    setDatumaID(latestDatumaID);
                }

                const currentDatums = datumsData.find(d => d.id === datumaID);
                setDatums(currentDatums || null);

                const laiksData = laiksResponse.data || [];
                setLaiksList(laiksData);

                const kabinetsData = kabinetsResponse.data || [];
                setKabinetsList(kabinetsData);

                setError(null);
            } catch (err) {
                console.error('API Error:', err.response?.data || err.message);
                setError(err.message || 'Failed to fetch data');
            } finally {
                setLoading(false);
            }
        };

        if (kurssName) {
            fetchData();
        }
    }, [kurssName, datumaID]);

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
        if (kabinetInfo.vieta === "Priekuļi") {
            return `P. ${kabinetInfo.skaitlis}`;
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

    const handlePreviousWeek = () => {
        setDatumaID(prevID => {
            const currentIndex = allDatums.findIndex(d => d.id === prevID);
            return currentIndex > 0 ? allDatums[currentIndex - 1].id : prevID;
        });
    };

    const handleNextWeek = () => {
        setDatumaID(prevID => {
            const currentIndex = allDatums.findIndex(d => d.id === prevID);
            return currentIndex < allDatums.length - 1 ? allDatums[currentIndex + 1].id : prevID;
        });
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
                    <div className="datuma-selector">
                        <button onClick={handlePreviousWeek} className='dateButton'>&larr;</button>
                        {datums && (
                        <div className="datums">
                            <strong>Nedēļas datums: </strong>
                            <strong>{moment(datums.PirmaisDatums).format('YYYY-MM-DD')} - {moment(datums.PedejaisDatums).format('YYYY-MM-DD')}</strong>
                        </div>
                        )}
                        <button onClick={handleNextWeek} className='dateButton'>&rarr;</button>
                    </div>

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