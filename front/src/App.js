import React, { useState, useEffect } from "react";

const Heap = () => {
    const [inputValue, setInputValue] = useState("");
    const [heapArray, setHeapArray] = useState([]);

    const handleInsert = async () => {
        if (!inputValue.trim()) return;

        try {
            const response = await fetch("http://localhost/projeto_final_estrutura/api/heap_backend.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    value: parseInt(inputValue),
                    heap: heapArray,
                }),
            });

            if (!response.ok) {
                throw new Error("Erro na requisição: " + response.statusText);
            }

            const data = await response.json();
            if (data && data.heap) {
                setHeapArray(data.heap);
            } else {
                console.error("Resposta inválida do backend:", data);
            }

            setInputValue("");
        } catch (error) {
            console.error("Erro ao inserir valor:", error);
        }
    };

    const fetchHeap = async () => {
        try {
            const response = await fetch("http://localhost/projeto_final_estrutura/api/heap_backend.php");

            if (!response.ok) {
                throw new Error("Erro na requisição: " + response.statusText);
            }

            const data = await response.json();
            if (data && data.heap) {
                setHeapArray(data.heap);
            } else {
                console.error("Resposta inválida do backend:", data);
            }
        } catch (error) {
            console.error("Erro ao buscar Heap:", error);
        }
    };

    useEffect(() => {
        fetchHeap();
    }, []);

    return (
        <div style={{ padding: "20px", textAlign: "center" }}>
            <h1>Representação do Heap</h1>
            <div>
                <input
                    type="number"
                    value={inputValue}
                    onChange={(e) => setInputValue(e.target.value)}
                    placeholder="Digite um número"
                />
                <button onClick={handleInsert}>Inserir</button>
            </div>
            <div style={{ marginTop: "20px", display: "flex", justifyContent: "center", gap: "10px" }}>
                {Array.isArray(heapArray) &&
                    heapArray.map((value, index) => (
                        <div
                            key={index}
                            style={{
                                width: "50px",
                                height: "50px",
                                border: "1px solid black",
                                display: "flex",
                                justifyContent: "center",
                                alignItems: "center",
                                backgroundColor: value ? "#f0f0f0" : "#ffffff",
                            }}
                        >
                            {value || ""}
                        </div>
                    ))}
            </div>
        </div>
    );
};

export default Heap;