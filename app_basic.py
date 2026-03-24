import streamlit as st
import tempfile
import os
from PyPDF2 import PdfReader
from langchain_text_splitters import RecursiveCharacterTextSplitter
from langchain_community.embeddings import HuggingFaceEmbeddings
from langchain_community.vectorstores import FAISS

@st.cache_resource
def load_llm():
    return None

@st.cache_resource
def load_embeddings():
    return HuggingFaceEmbeddings(model_name="all-MiniLM-L6-v2")

def extract_text_from_pdf(pdf_file):
    pdf_reader = PdfReader(pdf_file)
    text = ""
    for page in pdf_reader.pages:
        if page.extract_text():
            text += page.extract_text()
    return text

def extract_text_from_txt(txt_file):
    return txt_file.read().decode("utf-8")

def process_document(file):
    if file.name.endswith(".pdf"):
        text = extract_text_from_pdf(file)
    elif file.name.endswith(".txt"):
        text = extract_text_from_txt(file)
    else:
        st.error("Document format not supported!")
        return None
    
    text_splitter = RecursiveCharacterTextSplitter(
        chunk_size=1000,
        chunk_overlap=200,
        length_function=len
    )
    chunks = text_splitter.split_text(text)
    return chunks

def main():
    st.set_page_config(page_title="AI Document QA System", layout="wide")
    st.title("ðŸ“„ AI Question Answering System")
    st.markdown("Upload a PDF or Text document, and ask questions about its content!")

    # Initialize session state for vector store
    if "vector_store" not in st.session_state:
        st.session_state.vector_store = None

    with st.sidebar:
        st.header("1. Upload Document")
        uploaded_file = st.file_uploader("Upload a PDF or TXT file", type=["pdf", "txt"])
        
        if uploaded_file is not None:
            if st.button("Process Document"):
                with st.spinner("Extracting text and creating embeddings..."):
                    chunks = process_document(uploaded_file)
                    if chunks:
                        embeddings = load_embeddings()
                        vector_store = FAISS.from_texts(chunks, embeddings)
                        st.session_state.vector_store = vector_store
                        st.session_state.chunks = chunks
                        st.success(f"Document processed successfully! Created {len(chunks)} chunks with embeddings.")

    st.header("2. Document Chunks")
    if "chunks" in st.session_state:
        st.write(f"Total chunks created: {len(st.session_state.chunks)}")
        for i, chunk in enumerate(st.session_state.chunks[:5]):
            with st.expander(f"Chunk {i+1}"):
                st.text(chunk)

if __name__ == "__main__":
    main()
